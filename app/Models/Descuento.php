<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'descuentos';

    protected $fillable = [
        'inicio',
        'fin',
        'porcentaje',
        'fijo',
        'activo',
        'nivel',
    ];

    protected $casts = [
        'inicio' => 'date',
        'fin' => 'date',
        'porcentaje' => 'decimal:2',
        'fijo' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
