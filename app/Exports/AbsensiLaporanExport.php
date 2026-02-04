<?php

namespace App\Exports;

use App\Models\AbsensiHarian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;

class AbsensiLaporanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $kelasId;
    protected $sekolahId;

    public function __construct($startDate, $endDate, $kelasId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->kelasId = $kelasId;
        $this->sekolahId = Auth::user()->sekolah_id;
    }

    public function collection()
    {
        $query = AbsensiHarian::query()
            ->with(['siswa', 'siswa.kelas'])
            ->where('sekolah_id', $this->sekolahId)
            ->whereBetween('tanggal', [$this->startDate, $this->endDate]);

        // Jika filter kelas dipilih
        if ($this->kelasId && $this->kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) {
                $q->where('kelas_id', $this->kelasId);
            });
        }

        return $query->orderBy('tanggal')->orderBy('jam_masuk')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam Scan',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Status',
            'Keterangan',
            'Sumber Data'
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            $row->jam_masuk ?? '-',
            $row->siswa->nisn ?? '-',
            $row->siswa->nama_lengkap ?? '-',
            $row->siswa->kelas->nama_kelas ?? '-',
            $row->status,
            $row->keterangan,
            $row->sumber
        ];
    }
}