<x-filament-panels::page>
    <form wire:submit="cetakLaporan">
        {{ $this->form }}

        <div class="mt-6 flex justify-start">
            <x-filament::button type="submit" icon="heroicon-o-printer" size="lg" color="primary">
                Cetak Laporan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>