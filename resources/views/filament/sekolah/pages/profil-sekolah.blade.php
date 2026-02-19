<x-filament-panels::page>
    <form wire:submit="saveProfile">
        {{ $this->form }}

        <div class="mt-8">
            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Simpan
                </x-filament::button>
            </div>
        </div>
    </form>

    <div class="mt-8">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
