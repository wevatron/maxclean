<?php

use App\Http\Controllers\CuentaTicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrLoginController;
use App\Models\CorteCaja;
use App\Http\Controllers\CorteCajaPdfController;
use App\Http\Controllers\ProductoDotacionPdfController;
use App\Http\Controllers\TicketPrintController;

Route::get('/', function () {
    return redirect('/admin/login');
});


Route::get('/acceso/qr/{token}', [QrLoginController::class, 'login'])
    ->name('qr.login');

Route::get('/admin/cortes/{corte}/pdf', function (CorteCaja $corte) {
    return $corte->generarPdf();
})->middleware(['auth']);


Route::get('/cortes-caja/{corte}/pdf', [CorteCajaPdfController::class, 'show'])
    ->name('cortes-caja.pdf');


Route::middleware(['auth'])->get('/tickets/{ticket}/print', [TicketPrintController::class, 'show'])
    ->name('tickets.print');


Route::middleware(['auth'])->group(function () {
    Route::get('/cuentas/{cuenta}/ticket', [CuentaTicketController::class, 'show'])
        ->name('cuentas.ticket');

    Route::get('/productos/{producto}/dotaciones/pdf', [ProductoDotacionPdfController::class, 'show'])
        ->name('productos.dotaciones.pdf');
});
