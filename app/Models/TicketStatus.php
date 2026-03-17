<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    protected $fillable = [
        'nombre',
        'color',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}
