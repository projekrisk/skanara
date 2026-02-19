<?php

namespace App\Filament\Sekolah\Pages;

use App\Models\PaketLangganan;
use App\Models\Sekolah;
use App\Models\Transaksi;
use App\Models\TahunAjaran;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

class ProfilSekolah extends Page implements Forms\Contracts\HasForms, HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Profil Sekolah';
    protected static ?string $title = 'Profil & Jadwal';
    protected static ?string $slug = 'profil-sekolah';
    protected static string $view = 'filament.sekolah.pages.profil-sekolah';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->peran !== 'guru';
    }

    private function getSekolah()
    {
        $tenant = Filament::getTenant();
        if ($tenant) return $tenant;
        
        $user = Auth::user();
        if ($user && $user->id_sekolah) {
            return Sekolah::find($user->id_sekolah);
        }
        return null;
    }

    private function getSekolahId()
    {
        $sekolah = $this->getSekolah();
        return $sekolah ? $sekolah->id : null;
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->peran === 'admin_sekolah', 403);
        
        $sekolah = $this->getSekolah();
        
        if (!$sekolah) {
            Notification::make()->title('Data Sekolah tidak ditemukan')->danger()->send();
            return;
        }

        $this->cancelExpiredTransactions();
        
        // 1. Ambil atribut sekolah
        $formData = $sekolah->attributesToArray();

        // 2. Ambil data Jadwal Harian (Manual Load)
        // Kita masukkan ke dalam array data agar Repeater bisa membacanya
        $formData['jadwalHarian'] = $sekolah->jadwalHarian()->get()->toArray();

        // 3. Isi form dengan data gabungan
        $this->form->fill($formData);
    }

    protected function cancelExpiredTransactions(): void
    {
        $sekolahId = $this->getSekolahId();
        if (!$sekolahId) return;

        Transaksi::where('id_sekolah', $sekolahId)
            ->where('status', 'menunggu')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'ditolak']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    FileUpload::make('logo')
                        ->label('Logo Sekolah')
                        ->directory('sekolah-logo')
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'items-center justify-center']),
                ]),

                Section::make('Tahun Ajaran Aktif')
                    ->schema([
                        Placeholder::make('tahun_ajaran_aktif')
                            ->label('Tahun Ajaran Saat Ini')
                            ->content(function () {
                                $sekolahId = $this->getSekolahId();
                                if (!$sekolahId) return 'Data Sekolah tidak ditemukan.';
                                $aktif = TahunAjaran::where('id_sekolah', $sekolahId)->where('status_aktif', true)->first();
                                return $aktif ? "{$aktif->nama} (Semester " . ucfirst($aktif->semester) . ")" : 'Belum ada tahun ajaran aktif.';
                            }),
                    ]),

                Tabs::make('Data Sekolah')
                    ->tabs([
                        Tabs\Tab::make('Identitas')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('npsn')->label('NPSN')->disabled(),
                                TextInput::make('nama_sekolah')->label('Nama Sekolah')->required(),
                                TextInput::make('nama_admin')->label('Nama Kontak Admin'),
                                TextInput::make('email_admin')->label('Email Admin')->email(),
                                Textarea::make('alamat')->columnSpanFull(),
                                TextInput::make('nama_kepala_sekolah')->label('Nama Kepala Sekolah'),
                                TextInput::make('nip_kepala_sekolah')->label('NIP Kepala Sekolah'),
                            ])->columns(2),

                        Tabs\Tab::make('Jadwal Pelajaran')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Placeholder::make('info_jadwal')
                                    ->content('Masukkan jadwal untuk setiap hari (Senin, Selasa, dll). Jika hari tidak ada di list, dianggap LIBUR.'),
                                
                                Repeater::make('jadwalHarian')
                                    ->label('Jadwal Harian')
                                    // FIX: Hapus ->relationship() agar tidak crash di Custom Page
                                    // Kita akan handle simpan data ini secara manual di saveProfile()
                                    ->schema([
                                        Select::make('hari')
                                            ->options([
                                                'Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu',
                                                'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu', 'Minggu' => 'Minggu'
                                            ])
                                            ->required()
                                            ->distinct(),
                                            
                                        TimePicker::make('jam_masuk')->label('Jam Masuk')->required()->seconds(false),
                                        TextInput::make('toleransi_telat')->label('Toleransi (Menit)')->numeric()->default(15)->required(),
                                        TimePicker::make('jam_tutup_masuk')->label('Absen Ditutup')->required()->seconds(false),
                                        TimePicker::make('jam_pulang')->label('Jam Pulang')->required()->seconds(false),
                                    ])
                                    ->columns(5)
                                    ->defaultItems(5)
                                    ->reorderable(false)
                                    ->cloneable(true),
                            ]),

                        Tabs\Tab::make('Member Area')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Section::make('Status Langganan')
                                    ->schema([
                                        Placeholder::make('status')
                                            ->label('Status Akun')
                                            ->content(function () {
                                                $sekolah = $this->getSekolah();
                                                if (!$sekolah) return '-';
                                                $status = $sekolah->status_langganan;
                                                $colorClass = $status === 'aktif' ? 'text-green-600' : 'text-red-600';
                                                return new HtmlString("<span class='px-3 py-1 font-bold {$colorClass}'>" . strtoupper($status) . "</span>");
                                            }),
                                        Placeholder::make('expired')
                                            ->label('Berlaku Sampai')
                                            ->content(fn () => $this->getSekolah()?->langganan_berakhir_pada ? \Carbon\Carbon::parse($this->getSekolah()->langganan_berakhir_pada)->translatedFormat('d F Y') : '-'),
                                    ])->columns(2),
                                    
                                Actions::make([
                                    Action::make('upgrade_paket')
                                        ->label('Perpanjang / Upgrade Paket')
                                        ->icon('heroicon-o-sparkles')
                                        ->color('warning')
                                        ->form([
                                            Select::make('id_paket')->options(PaketLangganan::where('harga', '>', 0)->pluck('nama_paket', 'id'))->required(),
                                        ])
                                        ->action(function (array $data) { 
                                            // FIX: Gunakan Filament::getTenant() atau Auth user secara eksplisit
                                            // Jangan gunakan $this->getSekolahId() di dalam closure Action
                                            $tenant = Filament::getTenant();
                                            $sekolahId = $tenant ? $tenant->id : Auth::user()->id_sekolah;

                                            if (!$sekolahId) return;

                                            $paket = PaketLangganan::find($data['id_paket']);
                                            Transaksi::create([
                                                'id_sekolah' => $sekolahId,
                                                'id_paket' => $paket->id,
                                                'jumlah_bayar' => $paket->harga,
                                                'bukti_bayar' => null, 
                                                'status' => 'menunggu', 
                                                'tanggal_mulai' => now(), 
                                                'tanggal_berakhir' => now()->addDays($paket->durasi_hari), 
                                            ]);
                                            Notification::make()->title('Tagihan Dibuat')->success()->send();
                                        })
                                ])->alignCenter(),
                            ]),
                    ])->columnSpanFull(),
                    
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Transaksi::query()->with('paket')->where('id_sekolah', $this->getSekolahId())->latest())
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->date(),
                Tables\Columns\TextColumn::make('paket.nama_paket'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([]);
    }

    public function saveProfile(): void
    {
        // Ambil semua data dari form
        $state = $this->form->getState();
        $sekolah = $this->getSekolah();
        
        if ($sekolah) {
            // 1. Simpan data sekolah (kecuali jadwalHarian karena bukan kolom di tabel sekolah)
            // collect($state)->except(...) membuang key jadwalHarian sebelum update
            $sekolah->update(collect($state)->except(['jadwalHarian'])->toArray());
            
            // 2. Simpan Data Jadwal Harian (Manual Sync)
            // Hapus jadwal lama untuk sekolah ini
            $sekolah->jadwalHarian()->delete();

            // Masukkan jadwal baru dari Repeater
            if (!empty($state['jadwalHarian'])) {
                // Kita perlu menambahkan id_sekolah ke setiap item karena createMany butuh itu
                // (atau createMany via relasi otomatis mengisi foreign key)
                // ->createMany() via relasi otomatis mengisi 'id_sekolah'
                $sekolah->jadwalHarian()->createMany($state['jadwalHarian']);
            }

            Notification::make()->title('Profil & Jadwal Berhasil Disimpan')->success()->send();
        }
    }
}