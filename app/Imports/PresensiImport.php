<?php

namespace App\Imports;

use App\Models\Presensi;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PresensiImport implements ToModel, WithHeadingRow
{
    protected $idSekolah;

    public function __construct($idSekolah)
    {
        $this->idSekolah = $idSekolah;
    }

    public function model(array $row)
    {
        $nis = $row['nis'] ?? null;
        if (!$nis) return null; 

        $siswa = Siswa::where('id_sekolah', $this->idSekolah)
            ->where('nis', $nis)
            ->first();

        if (!$siswa) return null;

        try {
            $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggal = $row['tanggal']; 
        }

        $jamMasuk = $this->formatTime($row['jam_masuk'] ?? null);
        $jamKeluar = $this->formatTime($row['jam_keluar'] ?? null);

        return Presensi::updateOrCreate(
            [
                'id_sekolah' => $this->idSekolah,
                'id_siswa'   => $siswa->id,
                'tanggal'    => $tanggal,
            ],
            [
                'jam_masuk'        => $jamMasuk,
                'jam_keluar'       => $jamKeluar,
                'status_kehadiran' => strtolower($row['status_kehadiran'] ?? 'hadir'),
                'metode'           => strtolower($row['metode'] ?? 'import_excel'),
                'catatan'          => $row['catatan'] ?? 'Impor Data Kiosk',
                'created_by'       => auth()->id(),
            ]
        );
    }

    private function formatTime($time)
    {
        if (!$time) return null;
        try {
            if (is_numeric($time)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($time)->format('H:i:s');
            }
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}