<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-4">
            <div class="fi-avatar object-cover object-center fi-circular rounded-full h-10 w-10 fi-user-avatar">
                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-8 w-8 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16.6 3.4l1.2-2.1c.1-.2 0-.5-.2-.6-.2-.1-.5 0-.6.2l-1.2 2.1c-1.3-.6-2.8-.9-4.3-.9s-3 .3-4.3.9L6 1c-.1-.2-.4-.3-.6-.2-.2.1-.3.4-.2.6l1.2 2.1C3.5 5.3 1.5 8 1.5 11h21c0-3-2-5.7-4.9-7.6zM7.5 7.5c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5 1.5.7 1.5 1.5-.7 1.5-1.5 1.5zm9 0c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5 1.5.7 1.5 1.5-.7 1.5-1.5 1.5zM2 12v6c0 1.7 1.3 3 3 3h2v2c0 .6.4 1 1 1s1-.4 1-1v-2h6v2c0 .6.4 1 1 1s1-.4 1-1v-2h2c1.7 0 3-1.3 3-3v-6H2z"/>
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <h2 class="text-md font-bold text-gray-800 dark:text-white truncate">
                    {{ $pengaturan->judul ?? 'Download Aplikasi' }}
                    <span class="ml-1 text-xs bg-green-600 text-white px-1.5 py-0.5 rounded-full">v{{ $pengaturan->versi }}</span>
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 line-clamp-2">
                    {{ $pengaturan->deskripsi ?? 'Aplikasi Android untuk presensi.' }}
                </p>
            </div>

            <div class="flex-shrink-0">
                <x-filament::button
                    tag="a"
                    href="{{ $pengaturan->link_download }}"
                    target="_blank"
                    icon="heroicon-o-arrow-down-tray"
                    color="success"
                    size="sm"
                >
                    Download
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
