<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $guarded = ['id'];

    // Tambahkan 'created_by' ke fillable agar bisa diisi massal
    protected $fillable = [
        'id_sekolah',
        'id_siswa',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran',
        'metode',
        'catatan',
        'created_by' // Kolom baru untuk mencatat pembuat data
    ];

    // Relasi ke Tenant (WAJIB ADA untuk Filament Multi-tenancy)
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah');
    }

    // Relasi ke Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    // Relasi ke User pembuat data (Guru/Admin)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
