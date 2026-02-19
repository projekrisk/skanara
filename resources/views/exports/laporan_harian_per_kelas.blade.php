<!DOCTYPE html>
<html>
<head>
    <title>Laporan Harian Per Kelas</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        
        .page-break { page-break-after: always; }
        .page-container { margin-bottom: 30px; }

        .kop-table { width: 100%; border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 100px; text-align: center; }
        .logo-cell img { height: 80px; width: auto; }
        .text-cell { text-align: center; }
        .text-cell h2 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        
        .report-title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 15px; text-decoration: underline; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { border: none; padding: 2px; font-size: 12px; vertical-align: top; }
        .label-col { width: 100px; font-weight: bold; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; font-size: 11px; vertical-align: middle; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        
        .footer-table { width: 100%; margin-top: 20px; }
        .footer-table td { border: none; vertical-align: top; }

        .text-bolos { color: #ef4444; font-weight: bold; }
        .text-hadir { color: #16a34a; }
        .text-danger { color: #dc2626; }
        
        @media print { 
            .no-print { display: none; } 
            .page-break { page-break-after: always; }
            body { -webkit-print-color-adjust: exact; }
        }
        
        .print-btn { 
            position: fixed; top: 20px; right: 20px; 
            background-color: #3b82f6; color: white; 
            padding: 10px 20px; border: none; border-radius: 5px; 
            cursor: pointer; font-weight: bold; z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Cetak Semua Kelas</button>
    </div>

    @foreach($kelasList as $kelas)
        <div class="page-container {{ !$loop->last ? 'page-break' : '' }}">
            
            <table class="kop-table" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="logo-cell">
                        @if($sekolah->logo)
                            <img src="{{ asset('uploads/' . $sekolah->logo) }}" alt="Logo">
                        @endif
                    </td>
                    <td class="text-cell">
                        <h2>{{ $sekolah->nama_sekolah }}</h2>
                        <p>NPSN: {{ $sekolah->npsn }}</p>
                        <p>{{ $sekolah->alamat ?? '-' }}</p>
                    </td>
                </tr>
            </table>

            <div class="report-title">LAPORAN PRESENSI HARIAN KELAS</div>

            <table class="info-table" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label-col">Hari, Tanggal</td>
                    <td width="10px">:</td>
                    <td>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Kelas</td>
                    <td>:</td>
                    <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                </tr>
                <tr>
                    <td class="label-col">Wali Kelas</td>
                    <td>:</td>
                    <td>{{ $kelas->waliKelas->name ?? '-' }}</td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">NIS</th>
                        <th width="35%">Nama Siswa</th>
                        <th width="10%">Masuk</th>
                        <th width="10%">Pulang</th>
                        <th width="10%">Status</th>
                        <th width="15%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Ambil siswa dari relasi kelas --}}
                    @forelse($kelas->siswa()->where('status', 'aktif')->orderBy('nama_lengkap')->get() as $index => $siswa)
                        @php
                            // Ambil data presensi dari Collection yang sudah di-load di Controller
                            // Menggunakan keyBy('id_siswa') memudahkan akses tanpa query ulang
                            $dataPresensi = $presensiHariIni[$siswa->id] ?? null;
                            
                            $jamMasuk = '-';
                            $jamKeluar = '-';
                            $statusTampil = 'Alpa'; // Default jika tidak ada data
                            $catatan = '-';
                            $cssClass = 'text-danger'; // Default merah untuk Alpa

                            if ($dataPresensi) {
                                $jamMasuk = $dataPresensi->jam_masuk ? \Carbon\Carbon::parse($dataPresensi->jam_masuk)->format('H:i') : '-';
                                $jamKeluar = $dataPresensi->jam_keluar ? \Carbon\Carbon::parse($dataPresensi->jam_keluar)->format('H:i') : '-';
                                $rawStatus = $dataPresensi->status_kehadiran;
                                $catatan = $dataPresensi->catatan;

                                // Logika Tampilan Status & Bolos
                                if ($rawStatus == 'hadir' || $rawStatus == 'terlambat') {
                                    if (empty($dataPresensi->jam_masuk) || empty($dataPresensi->jam_keluar)) {
                                        $statusTampil = 'Bolos (Tdk Lengkap)';
                                        $cssClass = 'text-bolos';
                                    } else {
                                        $statusTampil = ucfirst($rawStatus);
                                        $cssClass = 'text-hadir';
                                    }
                                } else {
                                    $statusTampil = ucfirst($rawStatus);
                                    $cssClass = ($rawStatus == 'sakit' || $rawStatus == 'izin') ? 'text-black' : 'text-danger';
                                }
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
                            <td class="text-center">{{ $jamMasuk }}</td>
                            <td class="text-center">{{ $jamKeluar }}</td>
                            <td class="text-center font-bold {{ $cssClass }}">{{ $statusTampil }}</td>
                            <td>{{ $catatan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="footer-table" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="70%">
                        <div style="font-size: 10px; border: 1px solid #ddd; padding: 5px; width: 60%;">
                            <strong>Rekapitulasi Kelas {{ $kelas->nama_kelas }}:</strong><br>
                            @php
                                // Hitung manual dari loop siswa di atas (atau query terpisah)
                                // Sederhananya, jika ingin rekap, hitung di controller dan pass ke view
                            @endphp
                            <i>(Total Siswa: {{ $kelas->siswa()->where('status', 'aktif')->count() }})</i>
                        </div>
                    </td>
                    <td width="30%" class="text-center">
                        <p>Mengetahui,</p>
                        <p>Wali Kelas</p>
                        <br><br><br>
                        <p style="margin-bottom: 0;"><b><u>{{ $kelas->waliKelas->name ?? '....................................' }}</u></b></p>
                        <p style="margin-top: 2px;">NIP. {{ $kelas->waliKelas->nip ?? '-' }}</p>
                    </td>
                </tr>
            </table>

        </div>
    @endforeach

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>