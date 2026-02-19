<!DOCTYPE html>
<html>
<head>
    <title>Laporan Presensi</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        
        .kop-table { width: 100%; border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 100px; text-align: center; }
        .logo-cell img { height: 80px; width: auto; }
        .text-cell { text-align: center; }
        .text-cell h2 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .text-cell p { margin: 2px 0; font-size: 12px; }

        .report-title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 20px; text-decoration: underline; text-transform: uppercase; }

        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { border: none; padding: 2px; font-size: 12px; }
        .label-col { width: 100px; font-weight: bold; }
        .sep-col { width: 10px; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; font-size: 11px; vertical-align: middle; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        .footer-table { width: 100%; margin-top: 30px; }
        .footer-table td { border: none; vertical-align: top; }
        .rekap-box { border: 1px solid #000; border-collapse: collapse; width: 200px; }
        .rekap-box td { border: 1px solid #000; padding: 4px 8px; }
    </style>
</head>
<body>

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
                <p>{{ $sekolah->alamat ?? 'Alamat belum diisi' }}</p>
            </td>
        </tr>
    </table>

    <div class="report-title">LAPORAN PRESENSI SISWA</div>

    <table class="info-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="label-col">Periode</td>
            <td class="sep-col">:</td>
            <td>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label-col">Tahun Ajaran</td>
            <td class="sep-col">:</td>
            <td>{{ $sekolah->tahun_ajaran ?? '-' }} ({{ $sekolah->semester ?? '-' }})</td>
        </tr>
        @if($kelas)
        <tr>
            <td class="label-col">Kelas</td>
            <td class="sep-col">:</td>
            <td>{{ $kelas->nama_kelas }}</td>
        </tr>
        @endif
        <tr>
            <td class="label-col">Total Data</td>
            <td class="sep-col">:</td>
            <td>{{ count($presensi) }} Data</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="10%">NIS</th>
                <th width="23%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="10%">Masuk</th>
                <th width="10%">Pulang</th>
                <th width="10%">Status</th>
                <th width="10%">Ket</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensi as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $row->siswa->nis ?? '-' }}</td>
                <td>{{ $row->siswa->nama_lengkap ?? '-' }}</td>
                <td class="text-center">{{ $row->siswa->kelas->nama_kelas ?? '-' }}</td>
                <td class="text-center">{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') : '-' }}</td>
                <td class="text-center">{{ $row->jam_keluar ? \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') : '-' }}</td>
                <td class="text-center text-bold">{{ ucfirst($row->status_kehadiran) }}</td>
                <td>{{ $row->catatan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data presensi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <table class="footer-table" cellspacing="0" cellpadding="0">
        <tr>
            <td width="60%">
                <p style="margin-bottom: 5px;"><strong>Rekapitulasi:</strong></p>
                <table class="rekap-box">
                    <tr><td>Hadir</td><td class="text-center">{{ $presensi->where('status_kehadiran', 'hadir')->count() }}</td></tr>
                    <tr><td>Terlambat</td><td class="text-center">{{ $presensi->where('status_kehadiran', 'terlambat')->count() }}</td></tr>
                    <tr><td>Tidak Hadir (S/I/A)</td><td class="text-center">{{ $presensi->whereIn('status_kehadiran', ['sakit', 'izin', 'alpa'])->count() }}</td></tr>
                </table>
            </td>
            <td width="40%" class="text-center">
                <p>Dicetak Pada: {{ date('d/m/Y') }}</p>
                <p>Mengetahui,</p>
                <br><br><br>
                <p>_________________________</p>
            </td>
        </tr>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
