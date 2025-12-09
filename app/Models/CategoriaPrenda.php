<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaPrenda extends Model
{
    protected $table = 'categoria_prendas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function prendas()
    {
        return $this->hasMany(Prenda::class);
    }
}
