<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\AbsensiKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbsensiKelasController extends Controller
{
    /**
     * Store Absensi Kelas (Jurnal Guru)
     * Data hanya disimpan di 'absensi_kelas' (termasuk detail JSON).
     * TIDAK mengubah tabel 'presensi' (Kiosk).
     */
    public function store(Request $request)
    {
        Log::info('Absensi Kelas Request:', $request->all());

        $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'ketidakhadiran' => 'present|array',
        ]);

        $user = $request->user();
        
        DB::beginTransaction();
        try {
            // 1. Hitung Total Siswa Aktif di Kelas
            $totalSiswa = Siswa::where('id_kelas', $request->id_kelas)
                ->where('id_sekolah', $user->id_sekolah)
                ->where('status', 'aktif')
                ->count();

            if ($totalSiswa == 0) {
                return response()->json(['message' => 'Kelas ini tidak memiliki siswa aktif.'], 400);
            }

            // 2. Proses Data Ketidakhadiran untuk disimpan sebagai JSON
            $detailKehadiran = [];
            $sakit = 0; 
            $izin = 0; 
            $alpa = 0;

            if ($request->has('ketidakhadiran')) {
                foreach ($request->ketidakhadiran as $item) {
                    $status = strtolower($item['status']);
                    $ket = $item['keterangan'] ?? null;
                    
                    // Simpan ke array detail
                    $detailKehadiran[] = [
                        'id_siswa' => $item['id_siswa'],
                        'status' => $status,
                        'keterangan' => $ket
                    ];

                    // Hitung Statistik
                    if ($status == 'sakit') $sakit++;
                    elseif ($status == 'izin') $izin++;
                    elseif ($status == 'alpa') $alpa++;
                }
            }

            // Hitung Hadir (Total - Tidak Hadir)
            $hadir = $totalSiswa - ($sakit + $izin + $alpa);

            // 3. Simpan ke Tabel Jurnal (AbsensiKelas)
            // Menggunakan updateOrCreate agar bisa diedit di hari yang sama
            $jurnal = AbsensiKelas::updateOrCreate(
                [
                    'id_sekolah' => $user->id_sekolah,
                    'id_guru' => $user->id, 
                    'id_kelas' => $request->id_kelas,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'waktu_input' => $request->jam,
                    'jumlah_hadir' => $hadir,
                    'jumlah_sakit' => $sakit,
                    'jumlah_izin' => $izin,
                    'jumlah_alpa' => $alpa,
                    'detail_kehadiran' => $detailKehadiran, // Simpan array ke kolom JSON
                    'catatan' => "Input via Android"
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Jurnal kelas berhasil disimpan.',
                'rekap' => [
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Absensi Kelas Error: " . $e->getMessage());
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cek Status Per Kelas (Untuk mengisi checkbox di Android saat dibuka kembali)
     * Mengambil dari kolom JSON 'detail_kehadiran' di tabel absensi_kelas
     */
    public function cekStatusPerKelas(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'tanggal' => 'required|date',
        ]);

        $user = $request->user();

        // Cari data di tabel AbsensiKelas (Jurnal) milik guru ini
        $jurnal = AbsensiKelas::where('id_sekolah', $user->id_sekolah)
            ->where('id_guru', $user->id)
            ->where('id_kelas', $request->id_kelas)
            ->where('tanggal', $request->tanggal)
            ->first();

        $dataResponse = [];

        if ($jurnal && !empty($jurnal->detail_kehadiran)) {
            // Parsing JSON (Laravel otomatis cast jika di model sudah diatur, tapi untuk aman kita cek)
            $details = is_string($jurnal->detail_kehadiran) 
                ? json_decode($jurnal->detail_kehadiran, true) 
                : $jurnal->detail_kehadiran;

            if (is_array($details)) {
                foreach ($details as $item) {
                    $dataResponse[] = [
                        'id_siswa' => $item['id_siswa'],
                        'status_kehadiran' => $item['status'] // Key disesuaikan untuk Android (GuruViewModel expects status_kehadiran from cekStatusAbsensi?)
                        // Note: Di Android ViewModel cekStatusAbsensi me-return CekStatusResponse
                        // Pastikan di Android CekStatusResponse punya @SerializedName("status_kehadiran")
                    ];
                }
            }
        }
        
        return response()->json($dataResponse);
    }

    /**
     * Riwayat Jurnal Guru Hari Ini (Untuk Dashboard & List Riwayat di Android)
     */
    public function getHistoryToday(Request $request)
    {
        $user = $request->user();
        
        // Gunakan Timezone Jakarta
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        
        $data = AbsensiKelas::with(['kelas'])
            ->where('id_sekolah', $user->id_sekolah)
            ->where('id_guru', $user->id)
            ->whereDate('tanggal', $today)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Ambil nama siswa untuk detail (Optimasi Query)
        $allStudentIds = [];
        foreach ($data as $item) {
            $details = is_string($item->detail_kehadiran) ? json_decode($item->detail_kehadiran, true) : $item->detail_kehadiran;
            if (is_array($details)) {
                foreach ($details as $d) {
                    if (isset($d['id_siswa'])) $allStudentIds[] = $d['id_siswa'];
                }
            }
        }
        
        $studentNames = Siswa::whereIn('id', $allStudentIds)->pluck('nama_lengkap', 'id');

        // Format data agar sesuai dengan model Android
        $formatted = $data->map(function($item) use ($studentNames) {
            
            $details = is_string($item->detail_kehadiran) ? json_decode($item->detail_kehadiran, true) : $item->detail_kehadiran;
            $listDetailSiswa = [];

            if (is_array($details)) {
                foreach($details as $d) {
                    $id = $d['id_siswa'] ?? 0;
                    $listDetailSiswa[] = [
                        'nama' => $studentNames[$id] ?? "Siswa #$id",
                        'status' => $d['status'] ?? '-'
                    ];
                }
            }

            // Urutkan siswa berdasarkan nama
            usort($listDetailSiswa, function($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            
            // Payload JSON yang disisipkan ke string 'nama_siswa'
            $payload = [
                'h' => $item->jumlah_hadir,
                's' => $item->jumlah_sakit,
                'i' => $item->jumlah_izin,
                'a' => $item->jumlah_alpa,
                'details' => $listDetailSiswa
            ];

            return [
                // Masukkan JSON payload ke kolom 'nama_siswa' agar ViewModel Android bisa mem-parsingnya
                'nama_siswa' => json_encode($payload),
                'kelas' => $item->kelas->nama_kelas ?? '-',
                'tanggal' => $item->tanggal,
                'jam_masuk' => $item->waktu_input, 
                'status' => 'Terkirim'
            ];
        });

        return response()->json($formatted);
    }
}