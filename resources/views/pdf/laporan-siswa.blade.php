<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ketidakhadiran</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11pt; line-height: 1.3; }
        
        /* Header Kop Surat */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 3px solid #000; 
            padding-bottom: 10px; 
            position: relative;
            min-height: 60px;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: auto;
        }
        .school-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .report-title { font-size: 12pt; font-weight: bold; letter-spacing: 1px; }

        /* Info Siswa (Tabel Tanpa Border agar Rapi) */
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { border: none; padding: 2px; vertical-align: top; }
        .label-col { width: 80px; font-weight: bold; }
        .sep-col { width: 10px; text-align: center; }

        /* Tabel Data */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        
        /* Status Color */
        .status-Sakit { color: #0284c7; } /* Biru */
        .status-Alpha { color: #dc2626; } /* Merah */
        .status-Izin { color: #d97706; } /* Orange */
        .status-Telat { color: #b45309; } /* Amber */

        /* Footer */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            font-size: 9pt;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        .footer-left { float: left; }
        .footer-right { float: right; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <!-- Logika Logo -->
        @php
            $logoPath = null;
            if($siswa->sekolah->logo) {
                if(\Illuminate\Support\Facades\Storage::disk('uploads')->exists($siswa->sekolah->logo)) {
                    $logoPath = \Illuminate\Support\Facades\Storage::disk('uploads')->path($siswa->sekolah->logo);
                } elseif(file_exists(public_path('uploads/'.$siswa->sekolah->logo))) {
                    $logoPath = public_path('uploads/'.$siswa->sekolah->logo);
                }
            }
            // Fallback logo default
            if(!$logoPath && file_exists(public_path('images/default-logo.png'))) {
                $logoPath = public_path('images/default-logo.png');
            }
        @endphp
        
        @if($logoPath)
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo">
        @endif

        <div class="school-name">{{ $siswa->sekolah->nama_sekolah }}</div>
        <div class="report-title">LAPORAN REKAPITULASI KETIDAKHADIRAN SISWA</div>
    </div>

    <!-- Info Siswa Rapi -->
    <table class="meta-table">
        <tr>
            <td class="label-col">Nama</td>
            <td class="sep-col">:</td>
            <td>{{ strtoupper($siswa->nama_lengkap) }}</td>
        </tr>
        <tr>
            <td class="label-col">NISN</td>
            <td class="sep-col">:</td>
            <td>{{ $siswa->nisn }}</td>
        </tr>
        <tr>
            <td class="label-col">Kelas</td>
            <td class="sep-col">:</td>
            <td>{{ $siswa->kelas->nama_kelas }}</td>
        </tr>
    </table>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="30%">Tanggal</th>
                <th width="25%">Jam Scan</th>
                <th>Keterangan Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d F Y') }}</td>
                <td class="text-center">{{ $row->jam_masuk ?? '-' }}</td>
                <td class="status-{{ $row->status }} text-center">
                    <strong>{{ strtoupper($row->status) }}</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px; color: #777;">
                    <i>Tidak ada catatan ketidakhadiran (Siswa Rajin Masuk).</i>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            Skanara - {{ $siswa->sekolah->nama_sekolah }}
        </div>
        <div class="footer-right">
            Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </div>
    </div>
</body>
</html>