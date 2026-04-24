<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    protected $fillable = [
        'sucursal_id',
        'user_id',
        'cliente_id',
        'status_id',
        'numero',
        'tipo',
        'total',
        'modo_por_kilo',
        'kilos',
        'tipo_lavado_kilo',
        'precio_kilo',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
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
        return $this->pagos()
            ->where('metodo_pago', '!=', 'cancelado')
            ->sum('monto');
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



    public function scopeVisiblePara(Builder $query, $user)
    {
        // 👑 Super admin ve todo
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // 👨‍💼 Si pertenece a sucursales
        if ($user->sucursales()->exists()) {

            $sucursalesIds = $user->sucursales()->pluck('sucursals.id');

            return $query->whereIn('sucursal_id', $sucursalesIds);
        }

        // Si no tiene sucursales → no ve nada
        return $query->whereRaw('1 = 0');
    }

    public static function ordenProcesos(): array
    {
        return [
            'detallado',
            'lavado',
            'secado',
            'doblado y empaquetado',
            'entregado',
        ];
    }
    public function puedeCompletar(string $nombreProceso): bool
    {
        $orden = self::ordenProcesos();

        $index = array_search($nombreProceso, $orden);

        if ($index === false) {
            return false;
        }

        if ($index === 0) {
            return true;
        }

        $anterior = $orden[$index - 1];

        return $this->procesos()
            ->where('proceso', $anterior)
            ->where('completado', true)
            ->exists();
    }
}
