<x-filament::section class="h-full flex flex-col justify-center">
    <div class="flex items-start gap-4">
        <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
            <x-filament::icon icon="heroicon-m-device-phone-mobile" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
        </div>
        <div class="flex-1">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Download Aplikasi Android</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $desc }}</p>
            <div class="mt-4 flex items-center gap-3">
                <x-filament::button tag="a" href="{{ $url }}" target="_blank" icon="heroicon-m-arrow-down-tray" color="primary" size="sm">Download APK</x-filament::button>
                <span class="text-xs font-mono text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ $version }}</span>
            </div>
        </div>
    </div>
</x-filament::section>