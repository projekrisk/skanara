<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanSiswaController extends Controller
{
    public function print(Request $request, $id)
    {
        // 1. Ambil Data Siswa beserta Kelas dan Wali Kelas
        $siswa = Siswa::with(['kelas.waliKelas', 'sekolah'])->findOrFail($id);
        
        $sekolah = $siswa->sekolah;
        
        // 2. Ambil Filter Tanggal
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : now()->startOfMonth();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : now();

        // 3. Ambil Tahun Ajaran Aktif
        $tahunAjaran = TahunAjaran::where('id_sekolah', $sekolah->id)
            ->where('status_aktif', true)
            ->first();

        // 4. Ambil Data Presensi
        $presensi = Presensi::where('id_siswa', $id)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('tanggal', 'asc')
            ->get();

        // 5. Hitung Rekapitulasi
        $rekap = [
            'H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'B' => 0, 'T' => 0
        ];

        foreach ($presensi as $row) {
            $status = strtolower($row->status_kehadiran);
            
            // Logika Status
            if ($status == 'hadir') {
                $rekap['H']++;
            } elseif ($status == 'terlambat') {
                $rekap['T']++;
                $rekap['H']++; // Terlambat tetap dihitung hadir secara akumulasi
            } elseif ($status == 'sakit') {
                $rekap['S']++;
            } elseif ($status == 'izin') {
                $rekap['I']++;
            } elseif ($status == 'alpa') {
                $rekap['A']++;
            }

            // Logika Bolos (Hadir tapi jam tidak lengkap)
            if (($status == 'hadir' || $status == 'terlambat') && (empty($row->jam_masuk) || empty($row->jam_keluar))) {
                // Opsional: Jika ingin dihitung terpisah
                // $rekap['B']++; 
            }
        }

        return view('exports.laporan_siswa', compact(
            'siswa', 
            'sekolah', 
            'presensi', 
            'startDate', 
            'endDate', 
            'rekap',
            'tahunAjaran'
        ));
    }
}