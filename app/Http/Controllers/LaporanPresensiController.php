<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Filament\Facades\Filament;

class LaporanPresensiController extends Controller
{
    public function print(Request $request)
    {
        // Ambil ID Sekolah dari user yang sedang login (Multi-tenancy)
        $user = auth()->user();
        if (!$user || !$user->id_sekolah) {
            abort(403, 'Akses Ditolak');
        }

        $sekolahId = $user->id_sekolah;
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kelasId = $request->query('id_kelas');

        $query = Presensi::query()
            ->with(['siswa', 'siswa.kelas'])
            ->where('id_sekolah', $sekolahId)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('id_kelas', $kelasId);
            });
        }

        // URUTKAN SECARA ASCENDING (Lama ke Baru)
        $presensi = $query->orderBy('tanggal', 'asc')->get();
        
        $sekolah = Sekolah::find($sekolahId);
        $kelas = $kelasId ? Kelas::find($kelasId) : null;

        return view('exports.laporan_presensi', compact('presensi', 'sekolah', 'kelas', 'startDate', 'endDate'));
    }
}
