<?php
namespace App\Exports;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sekolahId;
    public function __construct($sekolahId) { $this->sekolahId = $sekolahId; }
    public function collection() { return Siswa::where('id_sekolah', $this->sekolahId)->with('kelas')->get(); }
    public function headings(): array { return ['NIS', 'NISN', 'Nama Lengkap', 'Kelas', 'Jenis Kelamin', 'No HP', 'Status']; }
    public function map($siswa): array { return [$siswa->nis, $siswa->nisn, $siswa->nama_lengkap, $siswa->kelas->nama_kelas ?? '-', $siswa->jenis_kelamin, $siswa->nomor_hp_ortu, $siswa->status]; }
}
