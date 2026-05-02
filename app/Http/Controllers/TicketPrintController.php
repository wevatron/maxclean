<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketPrintController extends Controller
{
    public function show(Request $request, Ticket $ticket)
    {
        abort_unless(
            Ticket::query()
                ->visiblePara(auth()->user())
                ->whereKey($ticket->id)
                ->exists(),
            403
        );

        $ticket->load([
            'cliente',
            'operador',
            'sucursal',
            'status',
            'items.prenda.precios',
            'servicios',
            'pagos',
            'procesos',
        ]);

        return view('prints.ticket-80mm', [
            'ticket' => $ticket,
            'autoPrint' => $request->boolean('autoprint', true),
        ]);
    }
}