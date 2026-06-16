<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DotacionInventario extends Model
{
    protected $table = 'dotacion_inventarios';

    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'user_id',
        'ticket_pago_id',
        'cantidad',
        'precio_compra',
        'total',
        'nota',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_compra' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(TicketPago::class, 'ticket_pago_id');
    }
}
