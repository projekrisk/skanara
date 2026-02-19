<x-filament-widgets::widget>
    <x-filament::section class="{{ $isExpired ? 'bg-danger-50 border-danger-200' : 'bg-warning-50 border-warning-200' }} border shadow-sm">
        <div class="flex items-start gap-4">
            
            {{-- Ikon --}}
            <div class="{{ $isExpired ? 'text-danger-600' : 'text-warning-600' }}">
                @if($isExpired)
                    <x-heroicon-m-exclamation-circle class="w-8 h-8" />
                @else
                    <x-heroicon-m-clock class="w-8 h-8" />
                @endif
            </div>

            {{-- Konten Teks --}}
            <div class="flex-1">
                <h3 class="text-lg font-bold {{ $isExpired ? 'text-danger-700' : 'text-warning-700' }}">
                    @if($isExpired)
                        Paket Langganan Telah Berakhir!
                    @else
                        Masa Aktif Paket Segera Habis
                    @endif
                </h3>
                
                <p class="mt-1 text-sm {{ $isExpired ? 'text-danger-600' : 'text-warning-600' }}">
                    @if($isExpired)
                        Masa aktif paket sekolah Anda telah berakhir pada <strong>{{ $expiryDateFormatted }}</strong>. 
                        Akses ke beberapa fitur mungkin akan dibatasi. 
                    @else
                        Masa aktif paket sekolah Anda tersisa <strong>{{ ceil($daysLeft) }} hari</strong> lagi 
                        (berakhir pada {{ $expiryDateFormatted }}).
                    @endif
                    Silakan lakukan perpanjangan agar layanan tetap berjalan lancar.
                </p>

                <div class="mt-3">
                    {{-- Tombol link ke halaman Profil Sekolah untuk perpanjang --}}
                    {{-- Menggunakan getUrl() agar ID Tenant otomatis disertakan --}}
                    <x-filament::button
                        :color="$isExpired ? 'danger' : 'warning'"
                        size="sm"
                        tag="a"
                        href="{{ \App\Filament\Sekolah\Pages\ProfilSekolah::getUrl() }}"
                    >
                        Perpanjang Sekarang
                    </x-filament::button>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>