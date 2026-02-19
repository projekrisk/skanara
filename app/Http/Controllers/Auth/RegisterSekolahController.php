<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\User;
use App\Models\PaketLangganan;
use App\Mail\SekolahActivationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegisterSekolahController extends Controller
{
    public function show()
    {
        return view('auth.register-sekolah');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'npsn' => 'required|numeric|unique:sekolah,npsn',
            'nama_sekolah' => 'required|string|max:255',
            'alamat' => 'required|string',
            'logo' => 'nullable|image|max:2048', // Validasi Gambar (Max 2MB)
            
            'nama_admin' => 'required|string|max:255',
            'foto_profil' => 'nullable|image|max:2048', // Validasi Gambar
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // 2. Handle Upload Logo Sekolah
            // Menggunakan disk 'public' yang sudah diset ke public_path('uploads') di filesystems.php
            $logoPath = null;
            if ($request->hasFile('logo')) {
                // File akan tersimpan di: public/uploads/sekolah-logo/
                $logoPath = $request->file('logo')->store('sekolah-logo', 'public');
            }

            // 3. Handle Upload Foto Profil Admin
            // Menggunakan disk 'public' yang sudah diset ke public_path('uploads') di filesystems.php
            $fotoProfilPath = null;
            if ($request->hasFile('foto_profil')) {
                // File akan tersimpan di: public/uploads/user-photos/
                $fotoProfilPath = $request->file('foto_profil')->store('user-photos', 'public');
            }

            // 4. Ambil Paket Free
            $paketFree = PaketLangganan::where('nama_paket', 'Free')->first();
            $durasi = $paketFree ? $paketFree->durasi_hari : 90;
            $namaPaket = $paketFree ? $paketFree->nama_paket : 'Free';

            // 5. Buat Data Sekolah (LENGKAP)
            $sekolah = Sekolah::create([
                'npsn' => $request->npsn,
                'nama_sekolah' => $request->nama_sekolah,
                'alamat' => $request->alamat, // Simpan Alamat
                'logo' => $logoPath,          // Simpan Path Logo (sekolah-logo/namafile.ext)
                
                // Simpan Data Kontak Admin di tabel Sekolah
                'nama_admin' => $request->nama_admin,
                'email_admin' => $request->email,
                
                'status_langganan' => $namaPaket,
                'langganan_berakhir_pada' => Carbon::now()->addDays($durasi),
            ]);

            // 6. Buat User Admin
            $token = Str::random(64);
            $user = User::create([
                'name' => $request->nama_admin,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'peran' => 'admin_sekolah',
                'id_sekolah' => $sekolah->id,
                'status' => 'inactive', 
                'activation_token' => $token,
                'foto_profil' => $fotoProfilPath, // Simpan Path Foto Profil (user-photos/namafile.ext)
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }

        // 7. Kirim Email Aktivasi
        try {
            Mail::to($user->email)->send(new SekolahActivationMail($user));
            return redirect()->route('register.notice')->with('email', $user->email);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim email: " . $e->getMessage());
            // Tetap redirect ke notice tapi dengan pesan warning di log
            return redirect()->route('register.notice')
                ->with('email', $user->email)
                ->with('warning', 'Email aktivasi gagal dikirim. Hubungi admin.');
        }
    }

    public function notice()
    {
        return view('auth.register-notice');
    }

    public function verify($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('filament.sekolah.auth.login')
                ->withErrors(['email' => 'Token aktivasi tidak valid atau kadaluarsa.']);
        }

        $user->update([
            'status' => 'aktif',
            'activation_token' => null,
            'email_verified_at' => now()
        ]);

        return redirect()->route('filament.sekolah.auth.login')
            ->with('status', 'Akun berhasil diaktifkan! Silakan login.');
    }
}