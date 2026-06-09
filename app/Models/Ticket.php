<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    protected $fillable = [
        'cuenta_id',
        'sucursal_id',
        'user_id',
        'cliente_id',
        'status_id',
        'numero',
        'tipo',
        'total',
        'descuento_aplicado',
        'modo_por_kilo',
        'kilos',
        'tipo_lavado_kilo',
        'precio_kilo',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'descuento_aplicado' => 'decimal:2',
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
        return $this->belongsToMany(Servicio::class, 'ticket_servicios')
            ->withPivot([
                'cantidad',
                'precio_unitario',
                'subtotal',
            ])
            ->withTimestamps();
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'ticket_productos')
            ->withPivot([
                'cantidad',
                'precio_unitario',
                'subtotal',
            ])
            ->withTimestamps();
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

    public static function generarNumero(int $sucursalId): int
    {
        $ultimoNumero = static::query()
            ->where('sucursal_id', $sucursalId)
            ->whereNotNull('numero')
            ->selectRaw('MAX(CAST(numero AS UNSIGNED)) as max_numero')
            ->value('max_numero');

        return max(((int) $ultimoNumero) + 1, 1);
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
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
    public function saldoPendiente(): float
    {
        $pagado = $this->pagos()
            ->where('cancelado', false)
            ->sum('monto');

        return max((float) $this->total - (float) $pagado, 0);
    }

    public function getTipoLavadoKiloLabelAttribute(): string
    {
        $tipoLavado = $this->tipo_lavado_kilo;

        if (! $tipoLavado) {
            return 'Sin especificar';
        }

        $catalogo = TipoKilo::query()
            ->where('clave', $tipoLavado)
            ->first();

        if ($catalogo) {
            return $catalogo->nombre;
        }

        return match ($tipoLavado) {
            'basico' => 'Básico',
            'premium' => 'Premium',
            'extra_lavado' => 'Extra lavado',
            'expres' => 'Expres',
            'ropa_interior' => 'Ropa interior',
            default => 'Sin especificar',
        };
    }

    public function getConceptosVentaAttribute()
    {
        $servicios = $this->relationLoaded('servicios')
            ? $this->servicios
            : $this->servicios()->get();

        $productos = $this->relationLoaded('productos')
            ? $this->productos
            : $this->productos()->get();

        return $servicios
            ->map(function ($item) {
                $item->tipo_venta = 'servicio';

                return $item;
            })
            ->concat($productos->map(function ($item) {
                $item->tipo_venta = 'producto';

                return $item;
            }))
            ->values();
    }
}
