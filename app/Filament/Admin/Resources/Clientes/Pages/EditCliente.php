<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClienteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

use Filament\Actions\Action;
use Illuminate\Support\Str;
use App\Models\LoginToken;


class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;


    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            Action::make('qrLogin')
    ->label('Acceso rápido (QR)')
    ->icon('heroicon-o-qr-code')
    ->color('primary')
    ->modalHeading('Acceso rápido del cliente')
    ->modalDescription('El cliente puede escanear este código para acceder a su cuenta.')
    ->modalSubmitAction(false)
    ->modalCancelActionLabel('Cerrar')

    // 🔥 AQUÍ SE GENERA EL TOKEN
    ->mountUsing(function () {

        LoginToken::where('user_id', $this->record->id)
            ->whereNull('used_at')
            ->delete();

        LoginToken::create([
            'user_id'    => $this->record->id,
            'token'      => Str::random(64),
            'expires_at' => now()->addMinutes(5),
        ]);
    })

    ->modalContent(function () {
        $token = LoginToken::where('user_id', $this->record->id)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $token) {
            return view('filament.clientes.qr-expirado');
        }

        return view('filament.clientes.qr-login', [
            'url'       => route('qr.login', $token->token),
            'expiresAt' => $token->expires_at,
        ]);
    })
        ];
    }



    protected function mutateFormDataBeforeSave(array $data): array
    {
        return \App\Filament\Admin\Resources\Clientes\Schemas\ClienteForm::mutateDataBeforeSave($data);
    }
}
