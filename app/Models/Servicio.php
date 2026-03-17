<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_base',
        'activo',
    ];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_servicios');
    }
}

