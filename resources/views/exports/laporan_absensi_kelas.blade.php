<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Kelas</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        
        @media print { 
            @page { size: landscape; margin: 10mm; }
            .no-print { display: none; } 
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        .kop-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 100px; text-align: center; }
        .logo-cell img { height: 80px; width: auto; }
        .text-cell { text-align: center; }
        .text-cell h2 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .text-cell p { margin: 2px 0; font-size: 12px; }
        
        .report-title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 20px; text-decoration: underline; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 15px; font-size: 12px; }
        .info-table td { padding: 4px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; font-size: 11px; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; vertical-align: middle; }
        
        .text-left { text-align: left !important; }
        .text-center { text-align: center !important; }
        .text-danger { color: #dc2626; }
        .text-warning { color: #d97706; }
        .text-primary { color: #2563eb; }
        
        .rekap-table { width: 100%; border-collapse: collapse; border: none; }
        .rekap-table td { border: none; padding: 1px 0; text-align: left; vertical-align: top; }
        
        ol.student-list { margin: 0; padding-left: 20px; text-align: left; }
        ol.student-list li { margin-bottom: 2px; }

        .print-btn { background-color: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" class="print-btn">🖨️ Cetak Laporan</button>
    </div>

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
                <p>{{ $sekolah->alamat ?? 'Alamat Sekolah tidak diisi' }}</p>
            </td>
        </tr>
    </table>

    <div class="report-title">LAPORAN ABSENSI KELAS</div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Periode</strong></td>
            <td width="2%">:</td>
            <td>{{ $namaBulan }} {{ $tahun }}</td>
            
            <td width="15%"><strong>Tahun Ajaran</strong></td>
            <td width="2%">:</td>
            <td>{{ $tahunAjaran->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Filter Kelas</strong></td>
            <td>:</td>
            <td>{{ $namaKelasFilter ?? 'Semua Kelas' }}</td>
            
            <td><strong>Semester</strong></td>
            <td>:</td>
            <td>{{ $tahunAjaran ? ucfirst($tahunAjaran->semester) : '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal / Jam</th>
                <th width="10%">Kelas</th>
                <th width="15%">Guru Pengampu</th>
                <th width="15%">Rekapitulasi</th>
                <th width="35%">Daftar Siswa Tidak Hadir</th>
                <th width="8%">Ket</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}<br>
                        <span style="color: #666;">Pukul: {{ \Carbon\Carbon::parse($row->waktu_input)->format('H:i') }}</span>
                    </td>
                    <td class="text-center">{{ $row->kelas->nama_kelas }}</td>
                    <td class="text-left">{{ $row->guru->name }}</td>
                    
                    <td>
                        <table class="rekap-table">
                            <tr>
                                <td width="50px">Hadir</td>
                                <td width="10px">:</td>
                                <td><strong>{{ $row->jumlah_hadir }}</strong></td>
                            </tr>
                            <tr>
                                <td>Sakit</td>
                                <td>:</td>
                                <td>{{ $row->jumlah_sakit }}</td>
                            </tr>
                            <tr>
                                <td>Izin</td>
                                <td>:</td>
                                <td>{{ $row->jumlah_izin }}</td>
                            </tr>
                            <tr>
                                <td>Alpa</td>
                                <td>:</td>
                                <td>{{ $row->jumlah_alpa }}</td>
                            </tr>
                        </table>
                    </td>

                    <td class="text-left">
                        @php
                            // Ambil Data Siswa Tidak Hadir
                            // Prioritaskan dari kolom JSON 'detail_kehadiran' jika ada (Fitur Baru)
                            $listSiswa = [];
                            
                            if (!empty($row->detail_kehadiran)) {
                                // Jika data JSON tersedia (Input Android/Update Baru)
                                $detailJson = is_string($row->detail_kehadiran) ? json_decode($row->detail_kehadiran, true) : $row->detail_kehadiran;
                                $ids = collect($detailJson)->pluck('id_siswa');
                                $students = \App\Models\Siswa::whereIn('id', $ids)->pluck('nama_lengkap', 'id');
                                
                                foreach($detailJson as $d) {
                                    $listSiswa[] = [
                                        'nama' => $students[$d['id_siswa']] ?? 'Siswa ID: '.$d['id_siswa'],
                                        'status' => $d['status']
                                    ];
                                }
                            } else {
                                // Fallback: Query ke tabel Presensi (Data Lama)
                                $siswaTidakHadir = \App\Models\Presensi::with('siswa')
                                    ->where('id_sekolah', $row->id_sekolah)
                                    ->where('tanggal', $row->tanggal)
                                    ->whereHas('siswa', function($q) use ($row) {
                                        $q->where('id_kelas', $row->id_kelas);
                                    })
                                    ->whereIn('status_kehadiran', ['sakit', 'izin', 'alpa'])
                                    ->get();
                                    
                                foreach($siswaTidakHadir as $p) {
                                    $listSiswa[] = [
                                        'nama' => $p->siswa->nama_lengkap,
                                        'status' => $p->status_kehadiran
                                    ];
                                }
                            }
                        @endphp

                        @if(empty($listSiswa))
                            <div style="text-align: center; color: #16a34a; font-weight: bold;">- NIHIL -</div>
                        @else
                            <ol class="student-list">
                                @foreach($listSiswa as $siswa)
                                    @php
                                        $warna = match(strtolower($siswa['status'])) {
                                            'sakit' => 'text-warning',
                                            'izin' => 'text-primary',
                                            'alpa' => 'text-danger',
                                            default => ''
                                        };
                                    @endphp
                                    <li>
                                        <strong>{{ $siswa['nama'] }}</strong> 
                                        <span class="{{ $warna }}" style="font-size: 10px;">({{ strtoupper($siswa['status']) }})</span>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </td>
                    <td>{{ $row->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data jurnal absensi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="info-table" style="margin-top: 30px;">
        <tr>
            <td width="70%"></td>
            <td width="30%" class="text-center">
                <p>Mengetahui,</p>
                <p>Kepala Sekolah</p>
                <br><br><br>
                <p style="margin-bottom: 0;"><b><u>{{ $sekolah->nama_kepala_sekolah ?? '....................................' }}</u></b></p>
                <p style="margin-top: 2px;">NIP. {{ $sekolah->nip_kepala_sekolah ?? '-' }}</p>
            </td>
        </tr>
    </table>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>