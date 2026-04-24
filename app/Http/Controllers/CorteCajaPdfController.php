<?php

namespace App\Http\Controllers;

use App\Models\CorteCaja;
use Barryvdh\DomPDF\Facade\Pdf;

class CorteCajaPdfController extends Controller
{
    public function show(CorteCaja $corte)
    {
        $corte->load([
            'pagos.ticket.cliente',
            'sucursal',
            'operador',
        ]);

        $pdf = Pdf::loadView('pdf.corte-caja', [
            'corte' => $corte,
        ]);

        return $pdf->stream('corte-' . $corte->id . '.pdf');
    }
}