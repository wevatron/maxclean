<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoKilo extends Model
{
    protected $table = 'tipos_kilos';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'precio',
        'minimo',
        'activo',
        'orden',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'minimo' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
