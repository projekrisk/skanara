<!DOCTYPE html>
<html>
<head>
    <title>Laporan Presensi Harian</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 10px; }
        
        .page-break { page-break-after: always; }
        .page-container { margin-bottom: 30px; }

        .kop-table { width: 100%; border-bottom: 1px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 100px; text-align: center; }
        .logo-cell img { height: 65px; width: auto; }
        .text-cell { text-align: left; }
        .text-cell h2 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        
        .report-title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px;text-transform: uppercase; }
        
        .info-container { width: 100%; margin-bottom: 15px; }
        .info-table { width: 100%; }
        .info-table td { border: none; padding: 2px; font-size: 12px; vertical-align: top; }
        .label-col { width: 100px; font-weight: bold; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4.5px 6px; font-size: 11px; vertical-align: middle; }
        .data-table th{padding: 7px;}
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
        <button onclick="window.print()" class="print-btn">🖨️ Cetak</button>
    </div>

    @foreach($dataPerKelas as $item)
        @php
            $kelas = $item['kelas'];
            $listSiswa = $item['data'];
        @endphp

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
                        <p style="margin:5px 0">NPSN: {{ $sekolah->npsn }}</p>
                        <p style="margin:5px 0">{{ $sekolah->alamat ?? '-' }}</p>
                    </td>
                </tr>
            </table>

            <div class="report-title">LAPORAN PRESENSI HARIAN</div>

            <table class="info-container" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="70%">
                        <table class="info-table">
                            <tr>
                                <td>Hari, Tanggal</td>
                                <td width="10px">:</td>
                                <td><strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Kelas</td>
                                <td>:</td>
                                <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                            </tr>
                        </table>
                    </td>
                    <td width="30%">
                        <table class="info-table">
                            <tr>
                                <td >Tahun Ajaran</td>
                                <td width="10px">:</td>
                                <td><strong>{{ $tahunAjaran->nama ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Semester</td>
                                <td>:</td>
                                <td><strong>{{ $tahunAjaran ? ucfirst($tahunAjaran->semester) : '-' }}</strong></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">NIS</th>
                        <th width="40%">Nama Siswa</th>
                        <th width="15%">Masuk</th>
                        <th width="15%">Pulang</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listSiswa as $index => $row)
                        @php
                            $siswa = $row['siswa'];
                            $presensi = $row['presensi'];
                            
                            $jamMasuk = '-';
                            $jamKeluar = '-';
                            $statusTampil = 'Alpa';
                            $cssClass = 'text-danger';

                            if ($presensi) {
                                $jamMasuk = $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-';
                                $jamKeluar = $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '-';
                                $rawStatus = $presensi->status_kehadiran;

                                if ($rawStatus == 'hadir' || $rawStatus == 'terlambat') {
                                    $jamJadwalPulang = $sekolah->jam_mulai_pulang ?? '15:00:00';
                                    $tglLaporan = \Carbon\Carbon::parse($tanggal);
                                    $isHariIni = $tglLaporan->isToday();
                                    $sekarang = now();
                                    $sudahWaktunyaPulang = $sekarang->format('H:i:s') >= $jamJadwalPulang;

                                    if (empty($presensi->jam_masuk)) {
                                         $statusTampil = 'Bolos (No In)';
                                         $cssClass = 'text-bolos';
                                    } elseif (empty($presensi->jam_keluar)) {
                                         if (!$isHariIni) {
                                             $statusTampil = 'Bolos';
                                             $cssClass = 'text-bolos';
                                         } elseif ($isHariIni && $sudahWaktunyaPulang) {
                                             $statusTampil = 'Bolos';
                                             $cssClass = 'text-bolos';
                                         } else {
                                             $statusTampil = ucfirst($rawStatus);
                                             $cssClass = 'text-hadir';
                                         }
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="footer-table" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="70%">
                         <table style="width: 95%; font-size: 10px; border-collapse: collapse;">
                            <tr>
                                <td style="width: 45%; vertical-align: top; padding-right: 10px;">
                                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                                        <tr><th colspan="2" style="border: 1px solid #000; padding: 4px; background-color: #f0f0f0;">Keterangan Kehadiran</th></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; width: 30px; text-align: center;"><b>H</b></td><td style="border: 1px solid #000; padding: 4px;">Hadir / Terlambat</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center;"><b>S</b></td><td style="border: 1px solid #000; padding: 4px;">Sakit</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center;"><b>I</b></td><td style="border: 1px solid #000; padding: 4px;">Izin</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center;"><b>A</b></td><td style="border: 1px solid #000; padding: 4px;">Alpa</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center; color: red;"><b>B</b></td><td style="border: 1px solid #000; padding: 4px;">Bolos (Tidak Lengkap)</td></tr>
                                    </table>
                                </td>
                                <td style="width: 55%; vertical-align: top;">
                                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                                        <tr><th colspan="2" style="border: 1px solid #000; padding: 4px; background-color: #f0f0f0;">Hari Libur Bulan Ini</th></tr>
                                        @forelse($hariLiburRaw as $libur)
                                            <tr>
                                                <td style="border: 1px solid #000; padding: 4px; width: 70px; text-align: center;">{{ \Carbon\Carbon::parse($libur->tanggal)->format('d/m/Y') }}</td>
                                                <td style="border: 1px solid #000; padding: 4px;">{{ $libur->keterangan }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" style="border: 1px solid #000; padding: 4px; text-align: center;">Tidak ada data hari libur.</td></tr>
                                        @endforelse
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="30%" class="text-center">
                        <p>Mengetahui,</p>
                        <p>Wali Kelas</p>
                        <br><br><br><br>
                        <p style="margin-bottom: 0;"><b><u>{{ $kelas->waliKelas->name ?? '....................................' }}</u></b></p>
                        <p style="margin-top: 2px;">NIP. {{ $kelas->waliKelas->nip ?? '-' }}</p>
                    </td>
                </tr>
            </table>

        </div>
    @endforeach
    
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>