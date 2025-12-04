<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMaquina extends Model
{
    protected $table = 'tipo_maquinas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'capacidad_kg',
        'tiempo_minimo',
        'tiempo_maximo',
    ];

    // Relación: un tipo puede tener muchas máquinas
  /*   public function machines()
    {
        return $this->hasMany(Machine::class);
    } */
}
