<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
    protected $table = 'maquinas';

    protected $fillable = [
        'sucursal_id',
        'tipo_maquina_id',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // La máquina pertenece a una sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    // La máquina pertenece a un tipo de máquina
    public function tipo()
    {
        return $this->belongsTo(TipoMaquina::class, 'tipo_maquina_id');
    }

    // Futuro: una máquina puede tener muchas órdenes asociadas
   /*  public function orders()
    {
        return $this->hasMany(Order::class);
    } */
}
