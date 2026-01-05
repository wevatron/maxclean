<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('sucursales')
                    ->label('Sucursales asignadas')
                    ->multiple()
                    ->relationship('sucursales', 'nombre')
                    ->preload()
                    ->searchable(),

                Select::make('roles')
                    ->label('Roles del usuario')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(fn ($state) => filled($state)) // Solo guarda si se escribió algo
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->helperText('Déjala vacía si no deseas cambiarla.')

            ]);
    }
}
