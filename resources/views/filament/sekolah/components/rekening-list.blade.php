<div class="grid grid-cols-1 gap-4">
    @foreach(\App\Models\RekeningBank::where('status_aktif', true)->get() as $bank)
        <div class="p-3 border rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center gap-3">
            <div class="bg-blue-100 p-2 rounded-full text-xl">🏦</div>
            <div>
                <h3 class="font-bold text-sm">{{ $bank->nama_bank }}</h3>
                <p class="font-mono text-primary-600 font-bold">{{ $bank->nomor_rekening }}</p>
                <p class="text-xs text-gray-500">a.n {{ $bank->atas_nama }}</p>
            </div>
        </div>
    @endforeach
</div>
