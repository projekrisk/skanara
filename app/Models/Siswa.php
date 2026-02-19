<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($siswa) {
            $appKey = env('APP_KEY'); 
            $dataString = $siswa->nis . $siswa->nama_lengkap . $appKey;
            $siswa->kode_qr_hash = hash('sha256', $dataString);
        });
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    // Relasi ke Presensi (HasMany)
    public function presensi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Presensi::class, 'id_siswa');
    }
}
