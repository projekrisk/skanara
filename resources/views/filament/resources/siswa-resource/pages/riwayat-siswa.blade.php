<x-filament-panels::page>
    <!-- Info Siswa Header -->
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $record->nama_lengkap }}</h2>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex gap-4">
            <span>NISN: <strong>{{ $record->nisn }}</strong></span>
            <span>Kelas: <strong>{{ $record->kelas->nama_kelas }}</strong></span>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    {{ $this->table }}
</x-filament-panels::page>