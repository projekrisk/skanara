<!DOCTYPE html>
<html>
<head>
    <title>Laporan Presensi Bulanan</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 15px; }
        
        @media print { 
            @page { size: landscape; margin: 10mm; }
            .no-print { display: none; } 
            .page-break { page-break-after: always; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        .kop-table { width: 100%; border-bottom: 1px solid #000; margin-bottom: 15px; padding-bottom: 5px; }
        .kop-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 100px; text-align: center; }
        .logo-cell img { height: 65px; width: auto; }
        .text-cell { text-align: left; }
        .text-cell h2 { margin: 0; font-size: 16px; text-transform: uppercase; font-weight: bold; }
        
        .report-title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; }
        
        .info-container { width: 100%; margin-bottom: 10px; }
        .info-table { width: 100%; }
        .info-table td { border: none; padding: 2px; font-size: 11px; vertical-align: top; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px; font-size: 10px; text-align: center; vertical-align: middle; }
        .data-table th{ padding: 7px 5px; }
        .data-table th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left !important; }
        
        .bg-holiday { background-color: #fca5a5 !important; } 
        .bg-sunday { background-color: #fecaca !important; }
        .bg-bolos { background-color: #ef4444 !important; color: white !important; }
        .bg-alpa { background-color: #fef2f2; color: #ef4444; font-weight: bold; }
        
        .footer-table { width: 100%; margin-top: 15px; }
        .footer-table td { border: none; vertical-align: top; }
        
        .print-btn { background-color: #3b82f6; color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-bottom: 15px; display: inline-block; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" class="print-btn">🖨️ Cetak</button>
    </div>

    @foreach($dataPerKelas as $item)
        @php
            $kelas = $item['kelas'];
            $siswa = $item['siswa'];
            // FIX: Menghapus baris $presensiMap = $item['presensiMap']; 
            // Karena $presensiMap sudah dikirim sebagai variabel global dari controller
        @endphp

        <div class="{{ !$loop->last ? 'page-break' : '' }}">
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

            <div class="report-title">LAPORAN REKAPITULASI PRESENSI BULANAN</div>

            <table class="info-container" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="80%">
                        <table class="info-table">
                            <tr>
                                <td width="80px">Bulan</td>
                                <td width="10px">:</td>
                                <td><strong>{{ $namaBulan }}</strong></td>
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
                                <td width="90px">Tahun Ajaran</td>
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
                        <th rowspan="2" width="2%">No</th>
                        <th rowspan="2" width="6%">NIS</th>
                        <th rowspan="2" width="16%">Nama Siswa</th>
                        <th colspan="{{ $daysInMonth }}">Tanggal</th>
                        <th colspan="5">Jumlah</th>
                    </tr>
                    <tr>
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            <th width="2%">{{ $i }}</th>
                        @endfor
                        <th width="2%">H</th>
                        <th width="2%">S</th>
                        <th width="2%">I</th>
                        <th width="2%">A</th>
                        <th width="2%">B</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa as $index => $row)
                        @php
                            $totH = 0; $totS = 0; $totI = 0; $totA = 0; $totB = 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->nis }}</td>
                            <td class="text-left" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $row->nama_lengkap }}</td>
                            
                            @for($i = 1; $i <= $daysInMonth; $i++)
                                @php
                                    $dateObj = \Carbon\Carbon::create($tahun, $bulan, $i);
                                    
                                    // 1. Cek Hari Libur Nasional
                                    $isHoliday = isset($hariLibur[$i]);

                                    // 2. Cek Jadwal Sekolah
                                    $namaHari = strtolower($dateObj->translatedFormat('l')); 
                                    $isSchoolDay = in_array($namaHari, $jadwalHariKerja ?? ['senin','selasa','rabu','kamis','jumat']);
                                    
                                    $isToday = $dateObj->isToday();
                                    $isPast = $dateObj->isPast();
                                    
                                    // Akses $presensiMap global
                                    $dataPresensi = $presensiMap[$row->id][$i] ?? null;
                                    $status = $dataPresensi ? $dataPresensi->status_kehadiran : null;
                                    
                                    $display = '';
                                    $bgClass = '';
                                    
                                    if ($isHoliday) {
                                        $bgClass = 'bg-holiday'; 
                                    } elseif (!$isSchoolDay) {
                                        $bgClass = 'bg-sunday'; 
                                    }
                                    
                                    if ($dataPresensi) {
                                        if (($status == 'hadir' || $status == 'terlambat')) {
                                            if (empty($dataPresensi->jam_masuk) || empty($dataPresensi->jam_keluar)) {
                                                $display = 'B'; $totB++; $bgClass = 'bg-bolos';
                                            } else {
                                                $display = 'H'; $totH++;
                                            }
                                        } elseif ($status == 'sakit') { 
                                            $display = 'S'; $totS++; 
                                        } elseif ($status == 'izin') { 
                                            $display = 'I'; $totI++; 
                                        } elseif ($status == 'alpa') { 
                                            $display = 'A'; $totA++; 
                                        }
                                    } else {
                                        if (!$isHoliday && $isSchoolDay) {
                                            if ($isPast && !$isToday) {
                                                $display = 'A'; $totA++; $bgClass = 'bg-alpa';
                                            } else {
                                                $display = '-';
                                            }
                                        }
                                    }
                                @endphp
                                <td class="{{ $bgClass }}">{{ $display }}</td>
                            @endfor
                            
                            <td style="font-weight: bold; background-color: #f8fafc;">{{ $totH }}</td>
                            <td style="font-weight: bold; background-color: #fefce8;">{{ $totS }}</td>
                            <td style="font-weight: bold; background-color: #eff6ff;">{{ $totI }}</td>
                            <td style="font-weight: bold; background-color: #fef2f2;">{{ $totA }}</td>
                            <td style="font-weight: bold; background-color: #fee2e2;">{{ $totB }}</td>
                        </tr>
                    @endforeach
                    
                    @if($siswa->isEmpty())
                        <tr>
                            <td colspan="{{ $daysInMonth + 8 }}">Tidak ada data siswa.</td>
                        </tr>
                    @endif
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
                                        <tr><td style="border: 1px solid #000; padding: 4px; width: 30px; text-align: center;"><b>H</b></td><td style="border: 1px solid #000; padding: 4px;">Hadir Lengkap</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center;"><b>S</b></td><td style="border: 1px solid #000; padding: 4px;">Sakit</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center;"><b>I</b></td><td style="border: 1px solid #000; padding: 4px;">Izin</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center;"><b>A</b></td><td style="border: 1px solid #000; padding: 4px;">Alpa</td></tr>
                                        <tr><td style="border: 1px solid #000; padding: 4px; text-align: center; background-color: #ef4444; color: white;"><b>B</b></td><td style="border: 1px solid #000; padding: 4px;">Bolos (Tidak Lengkap)</td></tr>
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
                    <td width="30%" style="text-align:center">
                        <p>Mengetahui,</p>
                        <p>Wali Kelas</p>
                        <br><br><br><br>
                        <p style="margin-bottom: 0;"><b><u>{{ $kelas->waliKelas->name ?? '_______________________' }}</u></b></p>
                        <p style="margin-top: 2px;">NIP. {{ $kelas->waliKelas->nip ?? '-' }}</p>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>