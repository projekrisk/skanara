<?php

namespace App\Filament\Pages;

use App\Imports\PresensiImport;
use App\Models\Sekolah;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage; // Tambahkan ini

class ImportPresensiPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Impor Presensi Kiosk';
    protected static ?string $title = 'Impor Presensi Kiosk';
    protected static ?string $slug = 'impor-presensi';
    protected static ?string $navigationGroup = 'Tools Admin';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.import-presensi-page';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->peran === 'super_admin';
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Formulir Impor')
                    ->description('Pastikan format Excel sesuai template: nis, tanggal, jam_masuk, jam_keluar, status_kehadiran.')
                    ->schema([
                        Select::make('id_sekolah')
                            ->label('Pilih Sekolah')
                            ->options(Sekolah::pluck('nama_sekolah', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Data siswa akan dicocokkan berdasarkan NIS di sekolah ini.'),
                        
                        FileUpload::make('file_excel')
                            ->label('File Excel (.xlsx)')
                            ->disk('public') // UBAH: Gunakan disk 'public' (mengarah ke public/uploads)
                            ->directory('temp-import') // File akan masuk ke public/uploads/temp-import
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->required(),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function import()
    {
        $data = $this->form->getState();

        // UBAH: Ambil path absolut melalui Disk Public agar sesuai dengan config filesystems
        $filePath = Storage::disk('public')->path($data['file_excel']);

        try {
            // Pastikan file benar-benar ada sebelum diproses
            if (!file_exists($filePath)) {
                throw new \Exception("File tidak ditemukan di: " . $filePath);
            }

            Excel::import(
                new PresensiImport($data['id_sekolah']), 
                $filePath
            );

            // Hapus file temp menggunakan Storage disk agar bersih
            Storage::disk('public')->delete($data['file_excel']);

            Notification::make()
                ->title('Impor Berhasil')
                ->body('Data presensi berhasil dimasukkan ke database.')
                ->success()
                ->send();

            $this->form->fill();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Impor')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}