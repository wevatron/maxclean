<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function puntos()
    {
        return $this->hasMany(Punto::class);
    }
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursal_user')
            ->withPivot('rol_en_sucursal')
            ->withTimestamps();
    }

    public function sucursalActivaId(): ?int
    {
        if (filled($this->sucursal_id ?? null)) {
            return (int) $this->sucursal_id;
        }

        if (session()->has('sucursal_id')) {
            return (int) session('sucursal_id');
        }

        $sucursales = $this->relationLoaded('sucursales')
            ? $this->sucursales
            : $this->sucursales()->get();

        if ($sucursales->count() === 1) {
            return (int) $sucursales->first()->id;
        }

        return $sucursales->first()?->id ? (int) $sucursales->first()->id : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {

            'admin' => $this->hasRole('super_admin')
                || $this->hasRole('empleado'),

            'cliente' => $this->hasRole('cliente'),

            default => false,
        };
    }
}
