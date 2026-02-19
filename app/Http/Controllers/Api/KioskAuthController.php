<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perangkat;
use App\Models\User;
use Illuminate\Http\Request;

class KioskAuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input device_id dari Android
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $rawDeviceId = $request->device_id;
        // Hash device ID yang dikirim dari Android untuk dicocokkan dengan database
        $hashedId = hash('sha256', $rawDeviceId);

        // Cari perangkat di database berdasarkan hash UID
        $perangkat = Perangkat::with('sekolah')->where('uid_perangkat', $hashedId)->first();

        // Cek 1: Apakah perangkat ditemukan?
        if (!$perangkat) {
            return response()->json(['message' => 'Perangkat tidak terdaftar. Pastikan ID sudah diinput di Admin Panel.'], 404);
        }

        // Cek 2: Apakah statusnya diizinkan?
        if ($perangkat->status !== 'diizinkan') {
            return response()->json(['message' => 'Aktivasi perangkat ditolak atau diblokir.'], 403);
        }

        // Cek 3: Cari Admin Sekolah pemilik perangkat ini untuk dipinjam identitasnya
        // Kiosk butuh token akses untuk hit API lain (sync, upload presensi)
        $adminSekolah = User::where('id_sekolah', $perangkat->id_sekolah)
                            ->whereIn('peran', ['admin_sekolah', 'operator'])
                            ->first();

        if (!$adminSekolah) {
            return response()->json(['message' => 'Sekolah tidak memiliki admin aktif.'], 500);
        }

        // Generate Token Sanctum atas nama Admin Sekolah
        // Beri nama token spesifik agar mudah dilacak: "Kiosk: Nama Perangkat"
        $token = $adminSekolah->createToken('Kiosk: ' . $perangkat->nama_perangkat)->plainTextToken;

        return response()->json([
            'message' => 'Aktivasi Berhasil',
            'token' => $token,
            'user' => [
                'id' => $adminSekolah->id, // Menggunakan ID Admin sebagai perwakilan
                'name' => $perangkat->nama_perangkat, // Nama User diganti Nama Perangkat untuk tampilan di App
                'role' => 'operator', // Force role menjadi operator/kiosk di sisi App
                'id_sekolah' => $perangkat->id_sekolah,
                'foto_profil' => null
            ],
            'sekolah' => $perangkat->sekolah, // Kirim data config sekolah
        ]);
    }
}