<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\AbsensiHarian;
use App\Models\Perangkat;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    // A. PULL DATA (Download Siswa & Settings ke HP)
    public function getSiswa(Request $request)
    {
        try {
            $sekolahId = null;

            // 1. Identifikasi: Apakah Guru (User) atau Kiosk (Device)?
            if ($request->user()) {
                // Login sebagai Guru (Bearer Token)
                $sekolahId = $request->user()->sekolah_id;
            } else {
                // Login sebagai Kiosk (Cek Header X-Device-Hash)
                $deviceHash = $request->header('X-Device-Hash');
                if ($deviceHash) {
                    $perangkat = Perangkat::where('device_id_hash', $deviceHash)->first();
                    if ($perangkat && $perangkat->status_aktif) {
                        $sekolahId = $perangkat->sekolah_id;
                    }
                }
            }

            if (!$sekolahId) {
                return response()->json(['message' => 'Unauthorized: Perangkat tidak dikenali atau sesi berakhir.'], 401);
            }

            // 2. Ambil Data Sekolah (Cek Masa Aktif)
            $sekolah = Sekolah::find($sekolahId);
            
            // Pastikan method isSubscriptionActive() ada di model Sekolah
            if (!$sekolah || !$sekolah->isSubscriptionActive()) {
                return response()->json(['message' => 'Masa aktif sekolah berakhir. Hubungi Admin.'], 403);
            }

            // 3. Ambil Siswa Aktif
            $siswa = Siswa::where('sekolah_id', $sekolahId)
                        ->where('status_aktif', true)
                        ->select('id', 'nama_lengkap', 'nisn', 'qr_code_data', 'kelas_id', 'foto') 
                        ->with('kelas:id,nama_kelas')
                        ->get();

            // 4. Format Settings (Jam & Hari Kerja)
            // Pastikan format jam H:i (Contoh: 07:00) untuk perbandingan string di Android
            $settings = [
                'jam_mulai_absen' => \Carbon\Carbon::parse($sekolah->jam_mulai_absen)->format('H:i'),
                'jam_masuk'       => \Carbon\Carbon::parse($sekolah->jam_masuk)->format('H:i'),
                'jam_pulang'      => \Carbon\Carbon::parse($sekolah->jam_pulang)->format('H:i'),
                'hari_kerja'      => $sekolah->hari_kerja ?? ['Senin','Selasa','Rabu','Kamis','Jumat'],
            ];

            return response()->json([
                'data' => $siswa,
                'settings' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    // B. PUSH DATA (Upload Absensi dari HP ke Server)
    public function uploadAbsensi(Request $request)
    {
        try {
            $data = $request->input('data');
            if (!$data || !is_array($data)) return response()->json(['message' => 'Invalid data format'], 400);

            $sekolah = null;
            $deviceHash = $request->header('X-Device-Hash');
            
            // Identifikasi Sekolah
            if ($deviceHash) {
                $perangkat = Perangkat::where('device_id_hash', $deviceHash)->first();
                if ($perangkat) $sekolah = $perangkat->sekolah;
            } elseif ($request->user()) {
                $sekolah = $request->user()->sekolah;
            }

            // Validasi Sekolah & Langganan
            if (!$sekolah || !$sekolah->isSubscriptionActive()) {
                return response()->json(['message' => 'Gagal Upload: Sekolah tidak aktif atau perangkat tidak dikenal.'], 403);
            }

            DB::beginTransaction();
            $savedCount = 0;
            foreach ($data as $row) {
                AbsensiHarian::updateOrCreate(
                    [
                        'siswa_id' => $row['siswa_id'], 
                        'tanggal' => $row['tanggal']
                    ],
                    [
                        'jam_masuk' => $row['jam_masuk'],
                        'status' => $row['status'],
                        'sumber' => 'Android',
                        'sekolah_id' => $sekolah->id,
                    ]
                );
                $savedCount++;
            }
            DB::commit();

            return response()->json(['message' => "Sukses sinkron $savedCount data"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}