<?php

namespace App\Http\Controllers;

use App\Models\DetailJurnal;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakRiwayatController extends Controller
{
    public function cetak(Request $request)
    {
        $siswaId = $request->query('siswa_id');
        $siswa = Siswa::with(['kelas', 'sekolah'])->findOrFail($siswaId);

        // Ambil data ketidakhadiran
        $riwayat = DetailJurnal::query()
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['Sakit', 'Izin', 'Alpha'])
            ->join('jurnal_guru', 'detail_jurnal.jurnal_guru_id', '=', 'jurnal_guru.id')
            ->select('detail_jurnal.*', 'jurnal_guru.tanggal', 'jurnal_guru.mata_pelajaran')
            ->orderBy('jurnal_guru.tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-siswa', compact('siswa', 'riwayat'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_Absensi_{$siswa->nisn}.pdf");
    }
}