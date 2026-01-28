<x-filament::section>
    @php
        $sekolah = Illuminate\Support\Facades\Auth::user()->sekolah;
        $paket = $sekolah->paket_langganan ?? 'free';
        $isPro = $paket !== 'free';
    @endphp

    <div class="flex flex-col md:flex-row items-center gap-6">
        <!-- Logo -->
        <div class="flex-shrink-0">
            @if($sekolah->logo)
                <img src="{{ asset('uploads/' . $sekolah->logo) }}" alt="Logo" class="w-20 h-20 rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-sm">
            @else
                <div class="w-20 h-20 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-2xl">
                    {{ substr($sekolah->nama_sekolah, 0, 1) }}
                </div>
            @endif
        </div>

        <!-- Info -->
        <div class="text-center md:text-left flex-1">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $sekolah->nama_sekolah }}
            </h2>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-2">
                <span class="px-2.5 py-0.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    NPSN: {{ $sekolah->npsn }}
                </span>
                
                <span class="px-2.5 py-0.5 rounded-md text-sm font-medium {{ $isPro ? 'bg-success-100 text-success-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($paket) }} Plan
                </span>
            </div>
        </div>

        <!-- Status Aktif -->
        <div class="text-right hidden md:block">
            <p class="text-sm text-gray-500 dark:text-gray-400">Masa Aktif Hingga</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">
                @if($sekolah->tgl_berakhir_langganan)
                    {{ \Carbon\Carbon::parse($sekolah->tgl_berakhir_langganan)->translatedFormat('d F Y') }}
                @else
                    Selamanya (Trial)
                @endif
            </p>
        </div>
    </div>
</x-filament::section>