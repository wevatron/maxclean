<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'sucursal_id',
        'nombre',
        'descripcion',
        'precio_base',
        'existencia',
        'activo',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'existencia' => 'integer',
        'activo' => 'boolean',
    ];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_productos')
            ->withPivot([
                'cantidad',
                'precio_unitario',
                'subtotal',
            ])
            ->withTimestamps();
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
