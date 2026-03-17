<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketItem extends Model
{
    protected $fillable = [
        'ticket_id',
        'prenda_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function prenda()
    {
        return $this->belongsTo(Prenda::class);
    }
}

