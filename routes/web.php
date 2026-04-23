<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrLoginController;
use App\Models\CorteCaja;

Route::get('/', function () {
    return redirect('/admin/login');
});


Route::get('/acceso/qr/{token}', [QrLoginController::class, 'login'])
    ->name('qr.login');

Route::get('/admin/cortes/{corte}/pdf', function (CorteCaja $corte) {
    return $corte->generarPdf();
})->middleware(['auth']);