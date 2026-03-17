<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketProceso extends Model
{
    protected $fillable = [
        'ticket_id',
        'proceso',
        'completado',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
