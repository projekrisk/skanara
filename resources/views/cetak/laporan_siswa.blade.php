<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan - {{ $siswa->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-white p-10 text-sm" style="font-family: Arial, sans-serif;">

    <div class="no-print mb-6 text-right">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-bold">
            🖨️ Cetak PDF
        </button>
    </div>

    <div class="flex items-center border-b-2 border-black pb-4 mb-6">
        @if($siswa->sekolah->logo)
            <img src="{{ asset('uploads/' . $siswa->sekolah->logo) }}" class="h-24 w-24 mr-6">
        @endif
        <div class="text-center flex-1">
            <h1 class="text-2xl font-bold uppercase">{{ $siswa->sekolah->nama_sekolah }}</h1>
            <p class="text-sm font-bold">NPSN: {{ $siswa->sekolah->npsn }}</p>
            <p class="text-gray-600">{{ $siswa->sekolah->alamat }}</p>
        </div>
    </div>

    <div class="text-center mb-6">
        <h2 class="text-lg font-bold uppercase underline">
            @if(isset($jenisLaporan) && $jenisLaporan === 'ketidakhadiran')
                LAPORAN KETIDAKHADIRAN SISWA
            @else
                LAPORAN RIWAYAT KEHADIRAN SISWA
            @endif
        </h2>
    </div>

    <table class="mb-6 w-full">
        <tr>
            <td class="w-32 font-bold">Nama</td>
            <td>: {{ $siswa->nama_lengkap }}</td>
            <td class="w-32 font-bold">Periode</td>
            <td>: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="font-bold">NIS / NISN</td>
            <td>: {{ $siswa->nis }} / {{ $siswa->nisn }}</td>
            <td class="font-bold">Tahun Ajaran</td>
            <td>: {{ $siswa->sekolah->tahun_ajaran ?? '-' }} ({{ $siswa->sekolah->semester ?? '-' }})</td>
        </tr>
        <tr>
            <td class="font-bold">Kelas</td>
            <td>: {{ $siswa->kelas->nama_kelas }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="w-full border-collapse border border-black mb-6">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-black p-2 text-center w-10">No</th>
                <th class="border border-black p-2 text-left">Tanggal</th>
                <th class="border border-black p-2 text-center">Jam Masuk</th>
                <th class="border border-black p-2 text-center">Jam Pulang</th>
                <th class="border border-black p-2 text-center">Status</th>
                <th class="border border-black p-2 text-left">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensi as $index => $row)
                <tr>
                    <td class="border border-black p-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black p-2">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d F Y') }}</td>
                    <td class="border border-black p-2 text-center">{{ \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') }}</td>
                    <td class="border border-black p-2 text-center">{{ $row->jam_keluar ? \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') : '-' }}</td>
                    <td class="border border-black p-2 text-center font-bold uppercase 
                        {{ $row->status_kehadiran == 'hadir' ? 'text-green-600' : 
                        ($row->status_kehadiran == 'Libur' ? 'text-blue-600' : 'text-red-600') }}">
                        {{ $row->status_kehadiran }}
                    </td>
                    <td class="border border-black p-2 text-xs text-gray-600">{{ $row->catatan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border border-black p-4 text-center text-gray-500">Tidak ada data presensi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex justify-between">
        <div class="w-1/3">
            <h3 class="font-bold mb-2">Rekapitulasi:</h3>
            <table class="w-full border-collapse border border-black text-sm">
                <tr><td class="border border-black p-1 px-2">Hadir</td><td class="border border-black p-1 px-2 font-bold text-center">{{ $rekap['hadir'] }}</td></tr>
                <tr><td class="border border-black p-1 px-2">Terlambat</td><td class="border border-black p-1 px-2 font-bold text-center">{{ $rekap['terlambat'] }}</td></tr>
                <tr><td class="border border-black p-1 px-2">Sakit</td><td class="border border-black p-1 px-2 font-bold text-center">{{ $rekap['sakit'] }}</td></tr>
                <tr><td class="border border-black p-1 px-2">Izin</td><td class="border border-black p-1 px-2 font-bold text-center">{{ $rekap['izin'] }}</td></tr>
                <tr><td class="border border-black p-1 px-2">Alpa</td><td class="border border-black p-1 px-2 font-bold text-center">{{ $rekap['alpa'] }}</td></tr>
            </table>
        </div>
        
        <div class="text-center mt-4">
            <p>Mengetahui,</p>
            <p class="mb-16">Wali Kelas</p>
            <p class="font-bold border-b border-black inline-block px-4">..........................</p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
