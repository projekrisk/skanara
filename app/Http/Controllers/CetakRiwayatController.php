<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian; // UPDATE Model
use App\Models\Siswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakRiwayatController extends Controller
{
    public function cetak(Request $request)
    {
        $siswaId = $request->query('siswa_id');
        $siswa = Siswa::with(['kelas', 'sekolah'])->findOrFail($siswaId);

        // Ambil data ketidakhadiran dari AbsensiHarian
        $riwayat = AbsensiHarian::query()
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['Sakit', 'Izin', 'Alpha', 'Telat'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-siswa', compact('siswa', 'riwayat'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_Absensi_{$siswa->nisn}.pdf");
    }
}