<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Barryvdh\DomPDF\Facade\Pdf;

class CorteCaja extends Model
{

protected $table = 'cortes_caja'; // 👈 IMPORTANTE
    protected $fillable = [
        'sucursal_id',
        'user_id',
        'fecha',
        'turno',
        'total',
        'total_efectivo',
        'total_tarjeta',
        'total_transferencia',
        'cerrado_en',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cerrado_en' => 'datetime',
    ];

    public function generarPdf()
{
    $this->load('pagos', 'sucursal', 'operador');

    return Pdf::loadView('pdf.corte-caja', [
        'corte' => $this,
    ])->download('corte-'.$this->id.'.pdf');
}

    public function pagos()
    {
        return $this->hasMany(TicketPago::class, 'corte_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}