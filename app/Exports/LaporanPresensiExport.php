<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Kelas;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanPresensiExport implements FromView, ShouldAutoSize
{
    protected $sekolahId;
    protected $startDate;
    protected $endDate;
    protected $kelasId;

    public function __construct($sekolahId, $startDate, $endDate, $kelasId = null)
    {
        $this->sekolahId = $sekolahId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->kelasId = $kelasId;
    }

    public function view(): View
    {
        $query = Presensi::query()
            ->with(['siswa', 'siswa.kelas'])
            ->where('id_sekolah', $this->sekolahId)
            ->whereBetween('tanggal', [$this->startDate, $this->endDate]);

        if ($this->kelasId) {
            $query->whereHas('siswa', function ($q) {
                $q->where('id_kelas', $this->kelasId);
            });
        }

        $data = $query->orderBy('tanggal', 'desc')->get();
        $sekolah = Sekolah::find($this->sekolahId);
        $kelas = $this->kelasId ? Kelas::find($this->kelasId) : null;

        return view('exports.laporan_presensi', [
            'presensi' => $data,
            'sekolah' => $sekolah,
            'kelas' => $kelas,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
