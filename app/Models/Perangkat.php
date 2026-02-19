<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perangkat extends Model
{
    use HasFactory;

    protected $table = 'perangkat';
    protected $guarded = ['id'];

    // --- LOGIKA OTOMATIS (ENKRIPSI DEVICE ID) ---
    protected static function booted()
    {
        // Saat membuat data baru, hash UID
        static::creating(function ($perangkat) {
            if (filled($perangkat->uid_perangkat)) {
                $perangkat->uid_perangkat = hash('sha256', $perangkat->uid_perangkat);
            }
        });

        // Saat update, hash UID HANYA JIKA diganti
        static::updating(function ($perangkat) {
            if ($perangkat->isDirty('uid_perangkat') && filled($perangkat->uid_perangkat)) {
                $perangkat->uid_perangkat = hash('sha256', $perangkat->uid_perangkat);
            }
        });
    }

    // --- RELASI ---
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah');
    }
}
