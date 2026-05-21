<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPago extends Model
{
    protected $fillable = [
        'ticket_id',
        'cuenta_id',
        'cuenta_pago_id',
        'proveedor_id',
        'metodo_pago',
        'monto',
        'referencia',
        'cancelado',
        'user_id',
        'sucursal_id',
        'tipo_movimiento',
        'categoria',
        'descripcion',
        'corte_id',
    ];

    protected $casts = [
        'cancelado' => 'boolean',
        'monto' => 'decimal:2',
    ];

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

    public function cuentaPago()
    {
        return $this->belongsTo(CuentaPago::class, 'cuenta_pago_id');
    }
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function corte(): BelongsTo
    {
        return $this->belongsTo(CorteCaja::class, 'corte_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
