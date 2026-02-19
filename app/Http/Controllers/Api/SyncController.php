<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\HariLibur;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncController extends Controller
{
    public function getMasterData(Request $request)
    {
        $user = $request->user();
        $sekolahId = $user->id_sekolah;
        $sekolah = $user->sekolah;

        $siswa = Siswa::where('id_sekolah', $sekolahId)
            ->where('status', 'aktif')
            ->select('id', 'nis', 'nama_lengkap', 'id_kelas', 'kode_qr_hash', 'foto')
            ->get();

        // 2. Ambil Data Kelas
        $kelas = Kelas::where('id_sekolah', $sekolahId)
            ->select('id', 'nama_kelas', 'tingkat')
            ->get();

        // 3. Ambil Data Hari Libur Nasional / Khusus
        $hariLibur = HariLibur::where('id_sekolah', $sekolahId)
            ->whereYear('tanggal', '>=', now()->year)
            ->get(['tanggal', 'keterangan']);

        // 4. Ambil Tahun Ajaran Aktif
        $tahunAjaran = TahunAjaran::where('id_sekolah', $sekolahId)
            ->where('status_aktif', true)
            ->first();

        // 5. Hari Libur Mingguan (Array)
        // Default [7] (Minggu) jika belum diset
        // $hariLiburMingguan = $sekolah->hari_libur_mingguan ?? [7];
        $jadwalHarian = \App\Models\JadwalHarian::where('id_sekolah', $sekolah->id)->get();

        return response()->json([
            'timestamp' => now()->toDateTimeString(),
            'nama_sekolah' => $sekolah->nama_sekolah,
            
            // Konfigurasi Sekolah
            'sekolah_config' => [
                'jam_masuk_mulai' => $sekolah->jam_mulai_masuk,
                'jam_masuk_akhir' => $sekolah->jam_akhir_masuk,
                'jam_pulang_mulai' => $sekolah->jam_mulai_pulang,
                'jam_pulang_akhir' => $sekolah->jam_akhir_pulang ?? null,
                'toleransi_telat' => $sekolah->toleransi_terlambat,
                // 'hariLiburMingguan' => $hariLiburMingguan, // Kirim Array ke Android
                'jadwal_harian' => $jadwalHarian,
            ],
            
            'status_langganan' => ucfirst($sekolah->status_langganan ?? 'Free'),
            'masa_aktif' => $sekolah->langganan_berakhir_pada ? Carbon::parse($sekolah->langganan_berakhir_pada)->format('d M Y') : '-',
            
            'tahun_ajaran' => $tahunAjaran ? $tahunAjaran->nama : '-',
            'semester' => $tahunAjaran ? ucfirst($tahunAjaran->semester) : '-',

            'total_siswa' => $siswa->count(),
            'siswa' => $siswa,
            'kelas' => $kelas,
            'hari_libur' => $hariLibur,
        ]);
    }

    /**
     * SYNC UP: Android mengirim data presensi offline.
     */
    public function uploadPresensi(Request $request)
    {
        Log::info('Upload Masuk:', ['count' => count($request->input('data', []))]);

        $request->validate([
            'data' => 'required|array',
            'data.*.id_siswa' => 'required',
            'data.*.tanggal' => 'required|date',
            'data.*.jam_masuk' => 'required',
            'data.*.status' => 'required',
        ]);

        $user = $request->user();
        $records = $request->input('data');
        $savedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($records as $record) {
                // 1. Validasi Siswa
                $siswa = Siswa::where('id', $record['id_siswa'])
                              ->where('id_sekolah', $user->id_sekolah)
                              ->first();

                if (!$siswa) continue;

                // 2. Cek Data Existing (Manual Check agar aman)
                $presensi = Presensi::where('id_sekolah', $user->id_sekolah)
                    ->where('id_siswa', $siswa->id)
                    ->where('tanggal', $record['tanggal'])
                    ->first();

                $waktuScan = $record['jam_masuk'];
                $statusInput = strtolower($record['status']);

                if ($presensi) {
                    // --- UPDATE DATA LAMA ---
                    if ($statusInput == 'pulang') {
                        // Jika scan Pulang, update jam_keluar
                        $presensi->jam_keluar = $waktuScan;
                    } else {
                        // Jika scan Masuk, update jam_masuk & status
                        $presensi->jam_masuk = $waktuScan;
                        $presensi->status_kehadiran = $record['status'];
                    }

                    $presensi->metode = 'scan'; 
                    $presensi->save();
                    $savedCount++;

                } else {
                    // --- INSERT DATA BARU ---
                    $presensi = new Presensi();
                    $presensi->id_sekolah = $user->id_sekolah;
                    $presensi->id_siswa = $siswa->id;
                    $presensi->tanggal = $record['tanggal'];
                    $presensi->created_by = $user->id;
                    $presensi->catatan = 'Sinkronisasi App';
                    $presensi->metode = 'scan';

                    if ($statusInput == 'pulang') {
                        // Kasus langka: Data baru langsung pulang
                        $presensi->jam_keluar = $waktuScan;
                        $presensi->jam_masuk = null;
                        $presensi->status_kehadiran = 'hadir'; 
                    } else {
                        // Normal: Scan Masuk
                        $presensi->jam_masuk = $waktuScan;
                        $presensi->jam_keluar = null;
                        $presensi->status_kehadiran = $record['status'];
                    }
                    
                    $presensi->save();
                    $savedCount++;
                }
            }
            
            DB::commit();
            Log::info("Sync Berhasil. Total: $savedCount");

            return response()->json([
                'message' => 'Sinkronisasi berhasil',
                'saved_records' => $savedCount
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("SYNC ERROR: " . $e->getMessage());
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
    
    public function getPresensiToday(Request $request)
    {
        $user = $request->user();
        
        // Ambil data presensi hari ini
        $data = Presensi::where('id_sekolah', $user->id_sekolah)
            ->whereDate('tanggal', now())
            ->get(['id_siswa', 'tanggal', 'jam_masuk', 'status_kehadiran as status']); // Alias status_kehadiran ke status

        return response()->json($data);
    }
    
    /**
     * API Diagnosa
     */
    public function checkConfig(Request $request)
    {
        $sekolahId = 1; // ID Default atau sesuaikan
        $sekolah = \App\Models\Sekolah::find($sekolahId);
        $tahunAjaran = TahunAjaran::where('id_sekolah', $sekolahId)->where('status_aktif', true)->first();

        return response()->json([
            'status' => 'OK',
            'data_sekolah' => [
                'nama' => $sekolah->nama_sekolah ?? '-',
                'jam_masuk' => $sekolah->jam_mulai_masuk ?? '-',
                'hari_libur_mingguan' => $sekolah->hari_libur_mingguan,
            ],
            'data_tahun_ajaran' => [
                'nama' => $tahunAjaran ? $tahunAjaran->nama : '-',
                'semester' => $tahunAjaran ? $tahunAjaran->semester : '-',
            ]
        ]);
    }

    public function getLaporanHarianMobile(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $user = $request->user();
        $tanggal = $request->tanggal;

        $kelas = \App\Models\Kelas::where('id_wali_kelas', $user->id)
            ->where('id_sekolah', $user->id_sekolah)
            ->first();

        if (!$kelas) {
            return response()->json(['message' => 'Anda bukan Wali Kelas.'], 403);
        }

        $siswaList = Siswa::where('id_kelas', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $presensi = Presensi::where('id_sekolah', $user->id_sekolah)
            ->whereDate('tanggal', $tanggal)
            ->whereIn('id_siswa', $siswaList->pluck('id'))
            ->get()
            ->keyBy('id_siswa');

        $daftarHadir = [];
        $rekap = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

        foreach ($siswaList as $siswa) {
            $dataPresensi = $presensi[$siswa->id] ?? null;
            
            $status = 'A';
            $jamMasuk = '-';
            $jamKeluar = '-';
            
            if ($dataPresensi) {
                $statusKode = strtolower($dataPresensi->status_kehadiran);
                if ($statusKode == 'hadir' || $statusKode == 'terlambat') {
                    $status = 'H';
                    $jamMasuk = $dataPresensi->jam_masuk ? Carbon::parse($dataPresensi->jam_masuk)->format('H:i') : '-';
                    $jamKeluar = $dataPresensi->jam_keluar ? Carbon::parse($dataPresensi->jam_keluar)->format('H:i') : '-';
                } elseif ($statusKode == 'sakit') {
                    $status = 'S';
                } elseif ($statusKode == 'izin') {
                    $status = 'I';
                }
            }

            if (isset($rekap[$status])) {
                $rekap[$status]++;
            }

            $daftarHadir[] = [
                'nama' => $siswa->nama_lengkap,
                'status' => $status,
                'jam_masuk' => $jamMasuk,
                'jam_pulang' => $jamKeluar
            ];
        }

        return response()->json([
            'kelas' => $kelas->nama_kelas,
            'tanggal' => Carbon::parse($tanggal)->translatedFormat('l, d F Y'),
            'rekap' => $rekap,
            'data' => $daftarHadir
        ]);
    }
    
    /**
     * LAPORAN BULANAN GURU
     * Menampilkan siswa yang tidak hadir (S/I/A) yang diinput oleh guru ini pada bulan tertentu.
     */
    public function getLaporanBulananGuru(Request $request)
    {
        $request->validate([
            'bulan' => 'required|numeric',
            'tahun' => 'required|numeric',
        ]);

        $user = $request->user();
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Ambil data presensi yang dibuat guru ini, di bulan ini, status bukan hadir
        $presensi = Presensi::with(['siswa.kelas'])
            ->where('id_sekolah', $user->id_sekolah)
            ->where('created_by', $user->id) // Filter inputan guru ini
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereIn('status_kehadiran', ['sakit', 'izin', 'alpa']) // Hanya ketidakhadiran
            ->get();

        // Grouping berdasarkan Siswa
        $grouped = $presensi->groupBy('id_siswa');
        
        $result = [];
        
        foreach ($grouped as $idSiswa => $items) {
            $siswa = $items->first()->siswa;
            
            $detail = $items->map(function($item) {
                return [
                    'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y'),
                    'status' => ucfirst($item->status_kehadiran),
                    'keterangan' => $item->catatan ?? '-'
                ];
            })->values();

            $result[] = [
                'nama_siswa' => $siswa->nama_lengkap,
                'kelas' => $siswa->kelas->nama_kelas ?? '-',
                'total_sakit' => $items->where('status_kehadiran', 'sakit')->count(),
                'total_izin' => $items->where('status_kehadiran', 'izin')->count(),
                'total_alpa' => $items->where('status_kehadiran', 'alpa')->count(),
                'detail' => $detail
            ];
        }

        // Urutkan berdasarkan nama siswa
        usort($result, function($a, $b) {
            return strcmp($a['nama_siswa'], $b['nama_siswa']);
        });

        return response()->json($result);
    }
}