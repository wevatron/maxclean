<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'sucursal_id',
        'user_id',
        'status_id',
        'numero',
        'tipo',
        'total',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    public function items()
    {
        return $this->hasMany(TicketItem::class);
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'ticket_servicios');
    }

    public function procesos()
    {
        return $this->hasMany(TicketProceso::class);
    }

    public function pagos()
    {
        return $this->hasMany(TicketPago::class);
    }

    public function getPagadoAttribute()
    {
        return $this->pagos()->sum('monto');
    }

    public function getSaldoAttribute()
    {
        return $this->total - $this->pagado;
    }

    public function getEstaLiquidadoAttribute()
    {
        return $this->saldo <= 0;
    }

    public static function generarNumero($sucursalId)
    {
        $ultimo = self::where('sucursal_id', $sucursalId)
            ->orderByDesc('numero')
            ->first();

        if (!$ultimo) {
            return 1;
        }

        return $ultimo->numero + 1;
    }
}
