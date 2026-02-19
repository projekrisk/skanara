<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
        <div class="flex items-center gap-4">
            @if($record->foto)
                <img src="{{ asset('uploads/' . $record->foto) }}" class="w-16 h-16 rounded-full object-cover border-2 border-primary-500">
            @else
                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xl">
                    {{ substr($record->nama_lengkap, 0, 1) }}
                </div>
            @endif
            <div>
                <h2 class="text-lg font-bold">{{ $record->nama_lengkap }}</h2>
                <p class="text-sm text-gray-500">{{ $record->nis }} / {{ $record->nisn }}</p>
            </div>
        </div>
        
        <div class="flex flex-col justify-center">
            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Kelas</span>
            <span class="text-md font-medium">{{ $record->kelas->nama_kelas ?? '-' }}</span>
        </div>

        <div class="flex flex-col justify-center">
            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                {{ ucfirst($record->status) }}
            </span>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
