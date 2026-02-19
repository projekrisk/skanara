<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Pelajar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
        .card {
            width: 53.98mm;
            height: 85.6mm;
            border: 1px solid #ccc;
            page-break-inside: avoid;
            background: white;
            position: relative;
        }
        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-bottom-left-radius: 50% 20%;
            border-bottom-right-radius: 50% 20%;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-gray-100 p-10 font-sans">

    <div class="no-print mb-8 flex gap-4 justify-center">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-full shadow-lg hover:bg-blue-700 font-bold transition">
            🖨️ Cetak Sekarang
        </button>
    </div>

    <div class="flex flex-wrap gap-8 justify-center">
        @foreach($dataSiswa as $siswa)
            <div class="card shadow-xl rounded-xl overflow-hidden flex flex-col items-center pt-6 text-center">
                
                <div class="z-10 mb-2">
                    @if($siswa->sekolah->logo)
                        <img src="{{ asset('uploads/' . $siswa->sekolah->logo) }}" class="h-16 w-16 bg-white rounded-full p-1 shadow-md object-contain">
                    @else
                        <div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center border-2 border-white shadow-md">
                            <span class="text-xs text-gray-500">Logo</span>
                        </div>
                    @endif
                </div>

                <div class="z-10 mb-4 px-2 w-full">
                    <h2 class="text-white text-xs font-bold uppercase tracking-wider drop-shadow-md truncate">{{ $siswa->sekolah->nama_sekolah }}</h2>
                    <p class="text-blue-100 text-[0.6rem] uppercase tracking-widest">Kartu Pelajar</p>
                </div>

                <div class="z-10 mb-2 relative w-32 h-32 flex justify-center items-center bg-white p-1 rounded-lg shadow-sm">
                    @php
                        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)
                            ->margin(1)
                            ->errorCorrection('H')
                            ->generate($siswa->kode_qr_hash);
                    @endphp
                    {!! $qrCode !!}

                    @if($siswa->sekolah->logo)
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-md p-0.5 border border-gray-100 flex items-center justify-center shadow-sm">
                            <img src="{{ asset('uploads/' . $siswa->sekolah->logo) }}" class="w-full h-full object-contain rounded-md">
                        </div>
                    @endif
                </div>

                <div class="w-full px-4 mb-auto">
                    <h1 class="text-sm font-extrabold text-gray-800 uppercase leading-tight mt-1">{{ $siswa->nama_lengkap }}</h1>
                    <p class="text-xs text-gray-500 font-mono mt-1">{{ $siswa->nis }}</p>
                    <p class="text-[0.65rem] text-gray-400 mt-0.5">{{ $siswa->kelas->nama_kelas ?? 'Umum' }}</p>
                </div>

                <div class="mb-4 w-full flex flex-col items-center justify-center">
                    <p class="text-[0.5rem] text-gray-300 mt-1 font-mono">{{ substr($siswa->kode_qr_hash, 0, 10) }}...</p>
                </div>

            </div>
        @endforeach
    </div>

</body>
</html>
