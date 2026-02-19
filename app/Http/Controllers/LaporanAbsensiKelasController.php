<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKelas;
use App\Models\Sekolah;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanAbsensiKelasController extends Controller
{
    public function print(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->id_sekolah) {
            abort(403, 'Akses Ditolak');
        }

        $sekolahId = $user->id_sekolah;
        $sekolah = Sekolah::find($sekolahId);
        
        $tahunAjaran = TahunAjaran::where('id_sekolah', $sekolahId)
            ->where('status_aktif', true)
            ->first();
            
        // Ambil Parameter Filter
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $idKelas = $request->query('id_kelas');

        // FIX: Definisikan nama bulan agar tidak error di View
        $namaBulan = Carbon::createFromDate($tahun, $bulan)->translatedFormat('F');

        // Filter Nama Kelas untuk Judul
        $namaKelasFilter = 'Semua Kelas';
        if ($idKelas) {
            $kelas = Kelas::find($idKelas);
            if ($kelas) {
                $namaKelasFilter = $kelas->nama_kelas;
            }
        }

        // Query Data Absensi Kelas (Jurnal)
        $query = AbsensiKelas::with(['kelas', 'guru'])
            ->where('id_sekolah', $sekolahId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($idKelas) {
            $query->where('id_kelas', $idKelas);
        }
        
        // Filter guru: Jika yang login adalah guru, hanya tampilkan data dia sendiri
        if ($user->peran === 'guru') {
            $query->where('id_guru', $user->id);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        return view('exports.laporan_absensi_kelas', compact(
            'sekolah', 
            'tahunAjaran', 
            'data', 
            'namaBulan', // Variabel ini yang sebelumnya hilang
            'bulan', 
            'tahun', 
            'namaKelasFilter'
        ));
    }
}