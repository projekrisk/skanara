<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Sekolah extends Model
{
    use HasFactory;
    protected $table = 'sekolah';
    protected $guarded = [];

    protected $casts = [
        'hari_kerja' => 'array',
        'status_aktif' => 'boolean',
        'tgl_berakhir_langganan' => 'date',
    ];

    public function users(): HasMany { return $this->hasMany(User::class, 'sekolah_id'); }

    public function isSubscriptionActive(): bool
    {
        if (!$this->status_aktif) return false;

        if ($this->tgl_berakhir_langganan === null) return false;

        return Carbon::now()->lte($this->tgl_berakhir_langganan->endOfDay());
    }
}