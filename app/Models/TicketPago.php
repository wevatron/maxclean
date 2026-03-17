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
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
