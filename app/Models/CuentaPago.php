<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaPago extends Model
{
    protected $table = 'cuenta_pagos';

    protected $fillable = [
        'cuenta_id',
        'cliente_id',
        'sucursal_id',
        'user_id',
        'monto',
        'metodo_pago',
        'referencia',
        'cancelado',
        'cancelado_en',
        'cancelado_por',
        'notas',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'cancelado' => 'boolean',
        'cancelado_en' => 'datetime',
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function ticketPagos(): HasMany
    {
        return $this->hasMany(TicketPago::class, 'cuenta_pago_id');
    }

    public function estaCancelado(): bool
    {
        return (bool) $this->cancelado;
    }
}