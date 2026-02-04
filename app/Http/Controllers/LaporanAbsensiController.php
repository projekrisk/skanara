<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiLaporanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanAbsensiController extends Controller
{
    public function download(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kelasId = $request->query('kelas_id');

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $namaFile = "Laporan_Absensi_{$startDate}_sd_{$endDate}_{$timestamp}.xlsx";

        return Excel::download(new AbsensiLaporanExport($startDate, $endDate, $kelasId), $namaFile);
    }
}