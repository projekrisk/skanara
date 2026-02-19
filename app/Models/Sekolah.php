<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasAvatar; // 1. Import Interface ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model implements HasName, HasAvatar // 2. Tambahkan HasAvatar
{
    use HasFactory;

    protected $table = 'sekolah';
    protected $guarded = ['id'];

    protected $casts = [
        'hari_libur_mingguan' => 'array', 
        'langganan_berakhir_pada' => 'date',
    ];

    public function getFilamentName(): string
    {
        return $this->nama_sekolah;
    }

    // 3. Tambahkan Method ini untuk URL Logo
    public function getFilamentAvatarUrl(): ?string
    {
        // Jika ada logo, kembalikan URL lengkapnya. 
        // Sesuaikan path 'uploads/' dengan konfigurasi filesystem Anda.
        return $this->logo ? asset('uploads/' . $this->logo) : null;
    }

    // --- RELASI ---

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_sekolah');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'id_sekolah');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'id_sekolah');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_sekolah');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'id_sekolah');
    }

    public function absensiKelas(): HasMany
    {
        return $this->hasMany(AbsensiKelas::class, 'id_sekolah');
    }

    public function perangkat(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Perangkat::class, 'id_sekolah');
    }
    
    public function hariLibur(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HariLibur::class, 'id_sekolah');
    }

    // Relasi ke Tahun Ajaran
    public function tahunAjaran(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TahunAjaran::class, 'id_sekolah');
    }
    
    public function jadwalHarian()
    {
        return $this->hasMany(JadwalHarian::class, 'id_sekolah');
    }
}