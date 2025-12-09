<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prenda extends Model
{
    protected $table = 'prendas';

    protected $fillable = [
        'categoria_prenda_id',
        'nombre',
        'tamano',
        'unidad',
        'descripcion',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaPrenda::class, 'categoria_prenda_id');
    }

    public function precios()
    {
        return $this->hasMany(PrecioPrenda::class);
    }
}
