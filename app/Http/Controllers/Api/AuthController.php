<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau Password salah'], 401);
        }

        // Cek status aktif
        $statusNonAktif = ['inactive', 'nonaktif', 'blokir', 'suspend', 0, false];
        if (in_array($user->status, $statusNonAktif, true)) {
             return response()->json(['message' => 'Akun Anda dinonaktifkan.'], 403);
        }

        // Cek Wali Kelas
        $kelasWali = Kelas::where('id_wali_kelas', $user->id)
            ->where('id_sekolah', $user->id_sekolah)
            ->first();

        $user->tokens()->delete();
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // TAMBAHAN: Pastikan NIP dikirim
                'nip' => $user->nip, 
                'role' => $user->peran,
                'foto_profil' => $user->foto_profil,
                'wali_kelas' => $kelasWali ? $kelasWali->nama_kelas : null, 
            ],
            'sekolah' => $user->sekolah
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}