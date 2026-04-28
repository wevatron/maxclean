<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'rfc',
        'direccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function gastos(): HasMany
    {
        return $this->hasMany(TicketPago::class, 'proveedor_id');
    }
}