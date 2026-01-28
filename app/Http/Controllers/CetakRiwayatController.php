<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian; 
use App\Models\Siswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CetakRiwayatController extends Controller
{
    public function cetak(Request $request)
    {
        $siswaId = $request->query('siswa_id');
        $siswa = Siswa::with(['kelas', 'sekolah'])->findOrFail($siswaId);

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now(); 

        if ($request->query('start_date') && $request->query('end_date')) {
            $startDate = Carbon::parse($request->query('start_date'));
            $endDate = Carbon::parse($request->query('end_date'));
        }

        $existingRecords = AbsensiHarian::query()
            ->where('siswa_id', $siswaId)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('tanggal'); 

        $hariKerjaSekolah = $siswa->sekolah->hari_kerja;
        
        if (empty($hariKerjaSekolah)) {
            $hariKerjaSekolah = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        }

        $mapHari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];

        $finalReport = collect([]);
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayNameEng = $date->format('l');
            $dayNameIndo = $mapHari[$dayNameEng]; 

            if (!in_array($dayNameIndo, $hariKerjaSekolah)) {
                continue; 
            }

            if ($existingRecords->has($dateStr)) {
                $record = $existingRecords->get($dateStr);
                
                if (in_array($record->status, ['Sakit', 'Izin', 'Alpha', 'Telat'])) {
                    $finalReport->push($record);
                }

            } else {
                $dummyAlpha = new AbsensiHarian();
                $dummyAlpha->tanggal = $dateStr;
                $dummyAlpha->jam_masuk = '-';
                $dummyAlpha->status = 'Alpha';
                
                $finalReport->push($dummyAlpha);
            }
        }

        $riwayat = $finalReport->sortBy('tanggal'); 

        $pdf = Pdf::loadView('pdf.laporan-siswa', compact('siswa', 'riwayat'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_Absensi_{$siswa->nisn}.pdf");
    }
}