<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiKelas extends Model
{
    use HasFactory;

    protected $table = 'absensi_kelas';

    protected $fillable = [
        'id_sekolah',
        'id_guru',
        'id_kelas',
        'tanggal',
        'waktu_input',
        'jumlah_hadir',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpa',
        'catatan',
        'detail_kehadiran', // <-- TAMBAHAN BARU
    ];

    // Agar kolom JSON otomatis diubah jadi Array oleh Laravel
    protected $casts = [
        'detail_kehadiran' => 'array',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}