<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursals'; // nombre correcto según la migración por defecto

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'whatsapp',
        'latitud',
        'longitud',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Una sucursal puede tener muchas máquinas
  /*   public function machines()
    {
        return $this->hasMany(Machine::class);
    } */

    // Una sucursal puede tener muchas órdenes
/*     public function orders()
    {
        return $this->hasMany(Order::class);
    } */

    // Si en el futuro deseas asignar empleados por sucursal
    public function empleados()
    {
        return $this->belongsToMany(User::class, 'sucursal_user');
    }
}
