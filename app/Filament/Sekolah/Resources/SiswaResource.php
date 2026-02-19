<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $tenantRelationshipName = 'siswa';
    protected static ?string $recordTitleAttribute = 'nama_lengkap';
    protected static ?string $slug = 'siswa';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Data Siswa';
    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_lengkap', 'nis', 'nisn'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'NIS' => $record->nis,
            'Kelas' => $record->kelas->nama_kelas ?? '-',
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return Pages\RiwayatPresensi::getUrl(['record' => $record]);
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->peran !== 'guru';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make('Data Pribadi')->schema([
                    TextInput::make('nis')
                        ->label('NIS')
                        ->required()
                        ->unique(
                            table: 'siswa',
                            column: 'nis',
                            ignoreRecord: true,
                            modifyRuleUsing: function (Unique $rule) {
                                $tenant = Filament::getTenant();
                                $sekolahId = $tenant ? $tenant->id : auth()->user()->id_sekolah;
                                return $rule->where('id_sekolah', $sekolahId);
                            }
                        ),
                    
                    TextInput::make('nisn')->label('NISN'),
                    TextInput::make('nama_lengkap')->label('Nama Lengkap')->required(),
                    Select::make('jenis_kelamin')
                        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                        ->required(),
                    TextInput::make('nomor_hp_ortu')->label('No. HP Orang Tua')->tel(),
                ])->columns(2),

                Card::make('Akademik & Foto')->schema([
                    Select::make('id_kelas')
                        ->label('Kelas')
                        ->relationship(
                            'kelas', 
                            'nama_kelas',
                            fn ($query) => $query
                                ->where('id_sekolah', Filament::getTenant()?->id ?? auth()->user()->id_sekolah)
                                ->orderByRaw('LENGTH(nama_kelas) ASC')
                                ->orderBy('nama_kelas', 'ASC')
                        )
                        ->searchable()->preload()->required(),
                    
                    Select::make('status')
                        ->options(['aktif' => 'Aktif', 'lulus' => 'Lulus', 'pindah' => 'Pindah', 'keluar' => 'Keluar'])
                        ->default('aktif')->required(),

                    FileUpload::make('foto')
                        ->disk('public')
                        ->directory('siswa-foto')
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nama_lengkap', 'asc')
            ->columns([
                ImageColumn::make('foto')->disk('public')->circular(),
                TextColumn::make('nis')->searchable(),
                TextColumn::make('nama_lengkap')->searchable()->sortable(),
                TextColumn::make('kelas.nama_kelas')->label('Kelas')->sortable(),
                TextColumn::make('jenis_kelamin'),
                ToggleColumn::make('status')->onColor('success')->offColor('danger')->disabled(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_kelas')
                    ->relationship('kelas', 'nama_kelas', fn ($query) => 
                        $query->where('id_sekolah', Filament::getTenant()?->id ?? auth()->user()->id_sekolah)
                              ->orderByRaw('LENGTH(nama_kelas) ASC')
                              ->orderBy('nama_kelas', 'ASC')
                    )
                    ->label('Filter Kelas'),
            ])
            ->headerActions([
                 Tables\Actions\Action::make('export_excel')
                    ->label('Ekspor')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $sekolahId = Filament::getTenant()?->id ?? auth()->user()->id_sekolah;
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\SiswaExport($sekolahId), 
                            'data_siswa.xlsx'
                        );
                    }),

                // --- AKSI IMPORT EXCEL (FIXED) ---
                Tables\Actions\Action::make('import_excel')
                    ->label('Impor Excel')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->closeModalByClickingAway(false)
                    ->form([
                        Forms\Components\Placeholder::make('template_link')
                            ->label('Template Excel')
                            ->content(new \Illuminate\Support\HtmlString('
                                <a href="'.route('download.template.siswa').'" target="_blank" class="text-primary-600 hover:text-primary-500 font-bold underline">
                                    ⬇️ Unduh Template .xlsx
                                </a>
                            ')),
                        Forms\Components\FileUpload::make('file_excel')
                            ->label('File .xlsx')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->disk('public') 
                            ->directory('temp-excel')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $sekolahId = Filament::getTenant()?->id ?? auth()->user()->id_sekolah;
                            
                            $filePath = public_path('uploads/' . $data['file_excel']);

                            if (!file_exists($filePath)) {
                                $filePath = storage_path('app/public/' . $data['file_excel']);
                            }
                            
                            if (!file_exists($filePath)) {
                                throw new \Exception("File excel tidak ditemukan. Silakan upload ulang.");
                            }

                            \Maatwebsite\Excel\Facades\Excel::import(
                                new \App\Imports\SiswaImport($sekolahId),
                                $filePath
                            );
                            
                            // Hapus file temp (aman dilakukan jika tidak ada redirect paksa)
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Sukses')
                                ->body('Data siswa berhasil diimpor.')
                                ->success()
                                ->send();

                            // JANGAN gunakan return redirect() di sini untuk menghindari error Livewire
                            
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Impor')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // --- AKSI IMPORT FOTO ZIP (FIXED) ---
                Tables\Actions\Action::make('import_foto_zip')
                    ->label('Impor Foto (ZIP)')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->closeModalByClickingAway(false)
                    ->form([
                        Forms\Components\FileUpload::make('file_zip')
                            ->label('File ZIP')
                            ->helperText('Isi ZIP: file foto dengan nama sesuai NIS (Contoh: 12345.jpg).')
                            ->disk('public')
                            ->directory('temp-zip')
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $zipPath = public_path('uploads/' . $data['file_zip']);
                        
                        if (!file_exists($zipPath)) {
                             $zipPath = storage_path('app/public/' . $data['file_zip']);
                        }

                        if (!file_exists($zipPath)) {
                             \Filament\Notifications\Notification::make()->title('Gagal')->body('File ZIP tidak ditemukan.')->danger()->send();
                            return;
                        }

                        $zip = new \ZipArchive;
                        $successCount = 0;
                        $failedCount = 0;
                        $sekolahId = Filament::getTenant()?->id ?? auth()->user()->id_sekolah;

                        if ($zip->open($zipPath) === TRUE) {
                            $destinationPath = public_path('uploads/siswa-foto');
                            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

                            for ($i = 0; $i < $zip->numFiles; $i++) {
                                $filename = $zip->getNameIndex($i);
                                $fileinfo = pathinfo($filename);
                                
                                if (str_ends_with($filename, '/') || str_contains($filename, '__MACOSX') || str_starts_with($fileinfo['basename'], '.')) continue;

                                $nis = $fileinfo['filename']; 
                                $ext = strtolower($fileinfo['extension'] ?? '');

                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                                    $siswa = Siswa::where('id_sekolah', $sekolahId)->where('nis', $nis)->first();

                                    if ($siswa) {
                                        $fileContent = $zip->getFromIndex($i);
                                        $newFileName = $sekolahId . '_' . $nis . '_' . time() . '.' . $ext;
                                        file_put_contents($destinationPath . '/' . $newFileName, $fileContent);
                                        $siswa->update(['foto' => 'siswa-foto/' . $newFileName]);
                                        $successCount++;
                                    } else {
                                        $failedCount++;
                                    }
                                }
                            }
                            $zip->close();
                            
                            // Hapus file ZIP
                            if (file_exists($zipPath)) {
                                @unlink($zipPath);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Impor Selesai')
                                ->body("Berhasil: $successCount foto. Gagal/NIS: $failedCount foto.")
                                ->success()
                                ->send();
                            
                            // JANGAN gunakan return redirect()
                        } else {
                            \Filament\Notifications\Notification::make()->title('Gagal')->body('Gagal membuka file ZIP.')->danger()->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('qr_code')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'QR: ' . $record->nama_lengkap)
                    ->modalContent(function ($record) {
                        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->margin(1)->errorCorrection('H')->generate($record->kode_qr_hash);
                        $logoUrl = ($record->sekolah && $record->sekolah->logo) ? asset('uploads/' . $record->sekolah->logo) : null;
                        
                        $logoImg = '';
                        if ($logoUrl) {
                            $logoImg = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 4px; border-radius: 8px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd;">
                                <img src="' . $logoUrl . '" style="width: 100%; height: 100%; object-fit: contain;">
                            </div>';
                        }
                        return new \Illuminate\Support\HtmlString('<div class="flex justify-center p-4 relative"><div style="position: relative;">'.$qrCode.$logoImg.'</div></div>');
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                
                Tables\Actions\Action::make('riwayat')
                    ->label('Riwayat')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->url(fn (Siswa $record) => Pages\RiwayatPresensi::getUrl(['record' => $record])),
                 
                 Tables\Actions\Action::make('cetak_presensi')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')->required()->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('end_date')->required()->default(now()),
                    ])
                    ->action(function (array $data, $record, $livewire) {
                        $url = route('cetak.laporan.siswa', [
                            'id' => $record->id,
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'jenis_laporan' => 'semua',
                        ]);
                        $livewire->js("window.open('{$url}', '_blank')");
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('cetak_kartu')
                        ->label('Cetak Kartu')
                        ->icon('heroicon-o-credit-card')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn ($records) => redirect()->route('cetak.kartu', ['ids' => $records->pluck('id')->implode(',')])),
                    
                    Tables\Actions\BulkAction::make('download_qr_zip')
                        ->label('Download QR (ZIP)')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $zip = new \ZipArchive;
                            $fileName = 'qr_codes_' . time() . '.zip';
                            $zipPath = public_path('uploads/' . $fileName);

                            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                                foreach ($records as $record) {
                                    $qrContent = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                                        ->size(500)->margin(2)->errorCorrection('H')->generate($record->kode_qr_hash);

                                    $qrImage = imagecreatefromstring($qrContent);
                                    if ($qrImage !== false) {
                                        $width = imagesx($qrImage);
                                        $height = imagesy($qrImage);
                                        $finalImage = imagecreatetruecolor($width, $height);
                                        $white = imagecolorallocate($finalImage, 255, 255, 255);
                                        imagefilledrectangle($finalImage, 0, 0, $width, $height, $white);
                                        imagecopy($finalImage, $qrImage, 0, 0, 0, 0, $width, $height);
                                        imagedestroy($qrImage);

                                        if ($record->sekolah && $record->sekolah->logo) {
                                            $logoPath = public_path('uploads/' . $record->sekolah->logo);
                                            if (file_exists($logoPath)) {
                                                $logoContent = file_get_contents($logoPath);
                                                $logoImage = imagecreatefromstring($logoContent);
                                                if ($logoImage !== false) {
                                                    $logoWidth = imagesx($logoImage);
                                                    $logoHeight = imagesy($logoImage);
                                                    $logoTargetWidth = $width * 0.2;
                                                    $scale = $logoTargetWidth / $logoWidth;
                                                    $logoTargetHeight = $logoHeight * $scale;
                                                    $dstX = ($width - $logoTargetWidth) / 2;
                                                    $dstY = ($height - $logoTargetHeight) / 2;
                                                    
                                                    $padding = 10;
                                                    imagefilledrectangle($finalImage, (int)($dstX - $padding), (int)($dstY - $padding), (int)($dstX + $logoTargetWidth + $padding), (int)($dstY + $logoTargetHeight + $padding), $white);
                                                    imagecopyresampled($finalImage, $logoImage, (int)$dstX, (int)$dstY, 0, 0, (int)$logoTargetWidth, (int)$logoTargetHeight, (int)$logoWidth, (int)$logoHeight);
                                                    imagedestroy($logoImage);
                                                }
                                            }
                                        }
                                        ob_start();
                                        imagejpeg($finalImage, null, 90);
                                        $jpgContent = ob_get_clean();
                                        imagedestroy($finalImage);
                                        $entryName = $record->nis . ' - ' . $record->nama_lengkap . '.jpg';
                                        $zip->addFromString($entryName, $jpgContent);
                                    }
                                }
                                $zip->close();
                                return response()->download($zipPath)->deleteFileAfterSend(true);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'), 
            'riwayat' => Pages\RiwayatPresensi::route('/{record}/riwayat'),
        ];
    }
}