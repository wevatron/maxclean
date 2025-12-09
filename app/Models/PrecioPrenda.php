<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioPrenda extends Model
{
    protected $table = 'precio_prendas';

    protected $fillable = [
        'prenda_id',
        'sucursal_id',
        'precio_normal',
        'precio_express',
        'precio_paquete',
        'piezas_por_paquete',
        'observaciones',
    ];

    public function prenda()
    {
        return $this->belongsTo(Prenda::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
