<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPago extends Model
{
protected $fillable = [
    'ticket_id',
    'metodo_pago',
    'monto',
    'referencia',
    'cancelado',
    'user_id',
    'sucursal_id',
    'corte_id',
];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function corte()
{
    return $this->belongsTo(CorteCaja::class, 'corte_id');
}
}
