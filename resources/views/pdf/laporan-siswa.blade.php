<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ketidakhadiran</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 14pt; font-weight: bold; }
        .subtitle { font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        .meta { margin-bottom: 15px; }
        .status-Sakit { color: blue; }
        .status-Alpha { color: red; }
        .status-Izin { color: orange; }
        .status-Telat { color: #d97706; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ strtoupper($siswa->sekolah->nama_sekolah) }}</div>
        <div class="subtitle">LAPORAN KETIDAKHADIRAN SISWA</div>
    </div>

    <div class="meta">
        <strong>Nama:</strong> {{ $siswa->nama_lengkap }}<br>
        <strong>NISN:</strong> {{ $siswa->nisn }}<br>
        <strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="40%">Tanggal</th>
                <th width="20%">Jam Scan</th>
                <th width="30%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $row->jam_masuk ?? '-' }}</td>
                <td class="status-{{ $row->status }}"><strong>{{ $row->status }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px;">Tidak ada riwayat ketidakhadiran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: right;">
        Dicetak pada: {{ date('d-m-Y H:i') }}
    </div>
</body>
</html>