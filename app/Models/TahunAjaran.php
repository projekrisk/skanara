<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TahunAjaran extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit karena nama tabel dalam bahasa Indonesia
    protected $table = 'tahun_ajaran';
    
    protected $guarded = ['id'];

    // Casting tipe data (misalnya boolean untuk status_aktif)
    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    // Relasi ke tabel sekolah
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah');
    }
}