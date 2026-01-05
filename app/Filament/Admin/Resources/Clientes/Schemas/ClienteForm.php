<?php

namespace App\Filament\Admin\Resources\Clientes\Schemas;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;


class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(150),

                TextInput::make('email')
                    ->label('Correo (opcional)')
                    ->email()
                    ->maxLength(150)
                    ->helperText('Si lo dejas vacío, se generará uno automáticamente con dominio @maxclean-oaxaca.com'),

                TextInput::make('password')
                    ->label('Contraseña (opcional)')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->helperText('Si la dejas vacía, se generará una contraseña segura automática.'),

                TextInput::make('whatsapp')
                    ->label('Teléfono/Whatsapp')
                    ->required()
                    ->maxLength(20)
                    ->tel(),

            ]);
    }


     /**
     * Antes de crear el registro — Filament 4
     */
    public static function mutateDataBeforeCreate(array $data): array
    {
        // 1️⃣ Generar correo automático
        if (empty($data['email'])) {

            $partes = explode(' ', strtolower($data['name']));
            $nombre = Str::slug($partes[0] ?? 'user');
            $apellido = Str::slug($partes[1] ?? 'cliente');

            $base = "{$nombre}.{$apellido}@maxclean-oaxaca.com";
            $correo = $base;
            $i = 1;

            while (User::where('email', $correo)->exists()) {
                $correo = "{$nombre}.{$apellido}{$i}@maxclean-oaxaca.com";
                $i++;
            }

            $data['email'] = $correo;
        }

        // 2️⃣ Generar password seguro si está vacío
        if (empty($data['password'])) {
            $data['password'] = bcrypt(Str::password());
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        return $data;
    }

    public static function mutateDataBeforeSave(array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']); // para no reemplazar la contraseña existente
        }

        return $data;
    }
}
