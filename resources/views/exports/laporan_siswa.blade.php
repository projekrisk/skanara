<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kehadiran Siswa</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        
        @media print { 
            .no-print { display: none; } 
            body { -webkit-print-color-adjust: exact; }
        }

        .kop-table { width: 100%; border-bottom: 1px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 100px; text-align: center; }
        .logo-cell img { height: 65px; width: auto; }
        .text-cell { text-align: left; }
        .text-cell h2 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        
        .report-title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px;text-transform: uppercase; }

        .biodata-table { width: 100%; margin-bottom: 20px; font-size: 12px; }
        .biodata-table td { padding: 3px; vertical-align: top; }
        .label-col { width: 80px; font-weight: bold; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; font-size: 11px; text-align: center; vertical-align: middle; }
        .data-table th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left !important; }

        .rekap-box { width: 60%; border: 1px solid #000; padding: 10px; margin-bottom: 20px; }
        .rekap-table { width: 100%; }
        .rekap-table td { padding: 2px; }

        .footer-table { width: 100%; margin-top: 30px; }
        .footer-table td { text-align: center; vertical-align: top; width: 33%; line-height:.7}
        
        .text-bolos { color: #ef4444; font-weight: bold; }
        .text-hadir { color: #16a34a; }
        .text-warning { color: #d97706; }
        .text-primary { color: #2563eb; }
        
        .print-btn { background-color: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; position: fixed; top: 20px; right: 20px; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">🖨️ Cetak</button>

    <table class="kop-table">
        <tr>
            <td class="logo-cell">
                @if($sekolah->logo)
                    <img src="{{ asset('uploads/' . $sekolah->logo) }}" alt="Logo">
                @endif
            </td>
            <td class="text-cell">
                <h2>{{ $sekolah->nama_sekolah }}</h2>
                <p style="margin:5px 0">NPSN: {{ $sekolah->npsn }}</p>
                <p style="margin:5px 0">{{ $sekolah->alamat ?? 'Alamat sekolah belum diisi' }}</p>
            </td>
        </tr>
    </table>

    <div class="report-title">LAPORAN KEHADIRAN SISWA</div>

    <table class="biodata-table">
        <tr>
            <td>Nama Siswa</td>
            <td width="10">:</td>
            <td width="47%"><strong>{{ $siswa->nama_lengkap }}</strong></td>
            
            <td>Tahun Ajaran</td>
            <td width="10">:</td>
            <td>{{ $tahunAjaran->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>:</td>
            <td>{{ $siswa->nis }} / {{ $siswa->nisn ?? '-' }}</td>
            
            <td>Semester</td>
            <td>:</td>
            <td>{{ $tahunAjaran ? ucfirst($tahunAjaran->semester) : '-' }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
            
            <td>Periode</td>
            <td>:</td>
            <td>{{ $startDate->format('d/m/Y') }} s.d {{ $endDate->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Hari, Tanggal</th>
                <th width="15%">Jam Masuk</th>
                <th width="15%">Jam Pulang</th>
                <th width="15%">Status</th>
                <th width="30%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensi as $index => $row)
                @php
                    $jamMasuk = $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') : '-';
                    $jamKeluar = $row->jam_keluar ? \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') : '-';
                    $status = ucfirst($row->status_kehadiran);
                    
                    $colorClass = '';
                    
                    if($row->status_kehadiran == 'alpa') {
                        $colorClass = 'text-bolos';
                    } elseif($row->status_kehadiran == 'sakit') {
                        $colorClass = 'text-warning';
                    } elseif($row->status_kehadiran == 'izin') {
                        $colorClass = 'text-primary';
                    } elseif($row->status_kehadiran == 'hadir' || $row->status_kehadiran == 'terlambat') {
                        if (empty($row->jam_masuk) || empty($row->jam_keluar)) {
                            $status = 'Bolos';
                            $colorClass = 'text-bolos';
                        } else {
                            $colorClass = 'text-hadir';
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d F Y') }}</td>
                    <td>{{ $jamMasuk }}</td>
                    <td>{{ $jamKeluar }}</td>
                    <td class="{{ $colorClass }}" style="font-weight: bold;">{{ $status }}</td>
                    <td class="text-left">{{ $row->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data kehadiran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="width: 40%;">
        <p style="font-weight: bold; margin-bottom: 5px;">Rekapitulasi:</p>
        <table class="data-table">
            <tr>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alpa</th>
                <th>Bolos</th>
                <th>Terlambat</th>
            </tr>
            <tr>
                <td>{{ $rekap['H'] }}</td>
                <td>{{ $rekap['S'] }}</td>
                <td>{{ $rekap['I'] }}</td>
                <td>{{ $rekap['A'] }}</td>
                <td class="text-bolos">{{ $rekap['B'] ?? 0 }}</td>
                <td>{{ $rekap['T'] }}</td>
            </tr>
        </table>
    </div>

    <table class="footer-table">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>Orang Tua/Wali</p>
                <br><br><br><br><br>
                <p>_______________________</p>
            </td>
            <td></td>
            <td>
                <p>Mengetahui,</p>
                <p>Wali Kelas</p>
                <br><br><br><br><br>
                <p style="font-weight: bold; text-decoration: underline;">
                    {{ $siswa->kelas->waliKelas->name ?? '.......................' }}
                </p>
                <p>NIP. {{ $siswa->kelas->waliKelas->nip ?? '-' }}</p>
            </td>
        </tr>
    </table>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>