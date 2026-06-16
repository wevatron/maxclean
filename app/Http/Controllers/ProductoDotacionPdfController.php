<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoDotacionPdfController extends Controller
{
    public function show(Producto $producto)
    {
        $producto->load([
            'sucursal',
            'dotaciones.sucursal',
            'dotaciones.usuario',
            'dotaciones.pago',
        ]);

        $pdf = Pdf::loadView('pdf.producto-dotaciones', [
            'producto' => $producto,
        ]);

        return $pdf->stream('dotaciones-producto-' . $producto->id . '.pdf');
    }
}
