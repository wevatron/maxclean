<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\TicketPago;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CuentaTicketController extends Controller
{
    public function show(Cuenta $cuenta)
    {
        $cuenta->load([
            'cliente',
            'sucursal',
            'operador',
            'pagos.operador',
            'tickets' => function ($query) {
                $query->with([
                    'status',
                    'pagos' => function ($subQuery) {
                        $subQuery
                            ->where('cancelado', false)
                            ->orderBy('created_at');
                    },
                ])
                    ->orderBy('id');
            },
        ]);

        $tickets = $cuenta->tickets;

        $totalTickets = $tickets->sum('total');

        $totalPagado = TicketPago::query()
            ->where('cuenta_id', $cuenta->id)
            ->where('cancelado', false)
            ->sum('monto');

        $saldo = max((float) $totalTickets - (float) $totalPagado, 0);

        $pagosAplicados = TicketPago::query()
            ->with('ticket')
            ->where('cuenta_id', $cuenta->id)
            ->where('cancelado', false)
            ->orderBy('created_at')
            ->get();

        $qrContenido = (string) $cuenta->id;

        $qrSvg = QrCode::format('svg')
            ->size(130)
            ->margin(1)
            ->generate($qrContenido);

        $qrBase64 = base64_encode($qrSvg);

        return view('tickets.cuenta-ticket', [
            'cuenta' => $cuenta,
            'tickets' => $tickets,
            'pagosAplicados' => $pagosAplicados,
            'totalTickets' => $totalTickets,
            'totalPagado' => $totalPagado,
            'saldo' => $saldo,
            'qrContenido' => $qrContenido,
            'qrBase64' => $qrBase64,
        ]);
    }
}