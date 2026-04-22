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

    public function original()
    {
        return $this->belongsTo(self::class, 'pago_original_id');
    }

    public function cancelaciones()
    {
        return $this->hasMany(self::class, 'pago_original_id');
    }
}
