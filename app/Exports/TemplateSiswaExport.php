<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithHeadings;
class TemplateSiswaExport implements WithHeadings
{
    public function headings(): array { return ['nis', 'nisn', 'nama_lengkap', 'kelas', 'jenis_kelamin', 'no_hp']; }
}
