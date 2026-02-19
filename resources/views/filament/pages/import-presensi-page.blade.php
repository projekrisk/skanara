<x-filament-panels::page>
    <form wire:submit="import">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg" color="success" icon="heroicon-m-arrow-up-tray">
                Mulai Proses Impor
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>