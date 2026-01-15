<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use Illuminate\Support\Facades\Auth;

class QrLoginController extends Controller
{
    public function login(string $token)
    {
       // $loginToken = LoginToken::where('token', $token)->first();
        $loginToken = LoginToken::where('token', $token)->firstOrFail();

        // Token no existe
        if (! $loginToken) {
            abort(404, 'Acceso inválido');
        }

        // Token ya usado o expirado
        if (! $loginToken->isValid()) {
            abort(403, 'Este acceso ya no es válido');
        }

        // Iniciar sesión
        Auth::login($loginToken->user);

        // Marcar token como usado
       // $loginToken->markAsUsed();

        // Redirigir al panel correcto
        return redirect('/cliente');
        //return redirect()->route('filament.cliente.pages.dashboard');
    }
}
