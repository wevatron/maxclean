<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Punto extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'asignado_por',
        'puntos',
        'fecha',
        'tikete',
        'sucursal_id',
    ];

    /**
     * Cliente que recibe los puntos
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Sucursal asociada (opcional)
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Usuario que asignó los puntos (operador/admin)
     */
    public function asignador()
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }
}
