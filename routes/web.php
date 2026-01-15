<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrLoginController;

Route::get('/', function () {
    return redirect('/admin/login');
});


Route::get('/acceso/qr/{token}', [QrLoginController::class, 'login'])
    ->name('qr.login');
