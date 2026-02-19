<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasTenants, HasAvatar // 2. Implement HasAvatar
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'peran',
        'id_sekolah',
        'foto_profil',
        'nip',
        'activation_token',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->foto_profil ? asset('uploads/' . $this->foto_profil) : null;
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // CEK STATUS AKTIF TERLEBIH DAHULU (PENTING AGAR TIDAK BISA LOGIN JIKA INACTIVE)
        if ($this->status !== 'aktif' && $this->peran !== 'super_admin') {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->peran === 'super_admin';
        }

        if ($panel->getId() === 'sekolah') {
            return in_array($this->peran, ['admin_sekolah', 'guru', 'operator']) && $this->id_sekolah;
        }

        return false;
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->sekolah ? collect([$this->sekolah]) : collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->id_sekolah == $tenant->id;
    }
}