<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    protected $table = 'cuentas';

    protected $fillable = [
        'cliente_id',
        'sucursal_id',
        'user_id',
        'numero',
        'total',
        'total_pagado',
        'saldo',
        'estatus',
        'abierta_en',
        'cerrada_en',
        'notas',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'saldo' => 'decimal:2',
        'abierta_en' => 'datetime',
        'cerrada_en' => 'datetime',
    ];

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

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'cuenta_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(CuentaPago::class, 'cuenta_id');
    }

    public function ticketPagos(): HasMany
    {
        return $this->hasMany(TicketPago::class, 'cuenta_id');
    }

    public function recalcularTotales(): void
    {
        $ticketIds = $this->tickets()->pluck('id');

        $total = $this->tickets()->sum('total');

        $totalPagado = TicketPago::query()
            ->whereIn('ticket_id', $ticketIds)
            ->where('cancelado', false)
            ->sum('monto');

        $saldo = max($total - $totalPagado, 0);

        $estatus = 'abierta';

        if ($saldo <= 0 && $total > 0) {
            $estatus = 'pagada';
        } elseif ($totalPagado > 0 && $saldo > 0) {
            $estatus = 'parcial';
        }

        $this->update([
            'total' => $total,
            'total_pagado' => $totalPagado,
            'saldo' => $saldo,
            'estatus' => $estatus,
            'cerrada_en' => $estatus === 'pagada' ? now() : null,
        ]);
    }

    public function estaPagada(): bool
    {
        return $this->estatus === 'pagada';
    }

    public function estaAbierta(): bool
    {
        return in_array($this->estatus, ['abierta', 'parcial'], true);
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max((float) $this->saldo, 0);
    }
}