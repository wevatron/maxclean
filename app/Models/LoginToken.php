<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class LoginToken extends Model
{
    protected $table = 'login_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    /**
     * Relación: token pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Indica si el token ya fue usado
     */
    public function isUsed(): bool
    {
        return ! is_null($this->used_at);
    }

    /**
     * Indica si el token ya expiró
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Indica si el token es válido para login
     */
    public function isValid(): bool
    {
        return ! $this->isUsed() && ! $this->isExpired();
    }

    /**
     * Marca el token como usado
     */
    public function markAsUsed(): void
    {
        $this->used_at = now();
        $this->save();
    }


    /**
     * Scope: solo tokens válidos
     */
    public function scopeValid($query)
    {
        return $query
            ->whereNull('used_at')
            ->where('expires_at', '>', now());
    }
}
