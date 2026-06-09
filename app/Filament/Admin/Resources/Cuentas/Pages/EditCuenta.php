<?php

namespace App\Filament\Admin\Resources\Cuentas\Pages;

use App\Filament\Admin\Resources\Cuentas\CuentaResource;
use App\Models\Cuenta;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditCuenta extends EditRecord
{
    protected static string $resource = CuentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('imprimirCuenta')
                ->label('Imprimir cuenta')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('cuentas.ticket', $this->record))
                ->openUrlInNewTab(),

            Action::make('recalcular')
                ->label('Recalcular cuenta')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn (): bool => $this->record->estatus !== 'cancelada')
                ->action(function () {
                    /** @var Cuenta $record */
                    $record = $this->record;

                    CuentaResource::recalcularCuenta($record);

                    $record->refresh();

                    $this->refreshFormData([
                        'total',
                        'total_pagado',
                        'saldo',
                        'estatus',
                        'cerrada_en',
                    ]);
                }),

            Action::make('abonarCuenta')
                ->label('Abonar / Liquidar cuenta')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn (): bool => (float) $this->record->saldo > 0 && in_array($this->record->estatus, ['abierta', 'parcial'], true))
                ->requiresConfirmation()
                ->modalHeading(fn () => 'Abonar a cuenta ' . $this->record->numero)
                ->modalDescription('Puedes abonar una parcialidad o liquidar el saldo completo. El pago se repartirá entre los tickets pendientes.')
                ->modalContent(fn () => $this->descuentoCuentaBadge())
                ->form([
                    TextInput::make('monto')
                        ->label('Monto a abonar')
                        ->prefix('$')
                        ->numeric()
                        ->minValue(fn () => (float) $this->record->saldo > 0 ? 0.01 : 0)
                        ->default(fn () => (float) $this->record->saldo)
                        ->required()
                        ->helperText(fn () => $this->helperSaldoCuenta()),

                    Select::make('metodo_pago')
                        ->label('Método de pago')
                        ->options([
                            'efectivo' => 'Efectivo',
                            'transferencia' => 'Transferencia',
                            'tarjeta' => 'Tarjeta',
                        ])
                        ->default('efectivo')
                        ->required(),
                ])
                ->action(function (array $data) {
                    /** @var Cuenta $record */
                    $record = $this->record;

                    $data['referencia'] = null;

                    CuentaResource::liquidarCuenta($record, $data);

                    $record->refresh();

                    $this->refreshFormData([
                        'total',
                        'total_pagado',
                        'saldo',
                        'estatus',
                        'cerrada_en',
                    ]);
                }),

            Action::make('cancelarCuenta')
                ->label('Cancelar cuenta')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $this->record->estatus !== 'cancelada')
                ->requiresConfirmation()
                ->modalHeading(fn () => 'Cancelar cuenta ' . $this->record->numero)
                ->modalDescription('Esta acción cancelará la cuenta, sus tickets, sus pagos y eliminará los puntos generados por esos tickets. No se permitirá si algún pago ya está incluido en un corte de caja.')
                ->form([
                    Textarea::make('motivo')
                        ->label('Motivo de cancelación')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->action(function (array $data) {
                    /** @var Cuenta $record */
                    $record = $this->record;

                    CuentaResource::cancelarCuenta($record, $data);

                    $record->refresh();

                    $this->refreshFormData([
                        'total',
                        'total_pagado',
                        'saldo',
                        'estatus',
                        'cerrada_en',
                        'notas',
                    ]);
                }),
        ];
    }

    protected function descuentoCuentaBadge(): ?HtmlString
    {
        $descuentoGuardado = (float) ($this->record->descuento_aplicado ?? 0);

        if ($descuentoGuardado > 0) {
            return new HtmlString('
                <div style="
                    margin-bottom:16px;
                    padding:12px 14px;
                    border-radius:14px;
                    border:1px solid #bfdbfe;
                    background:#eff6ff;
                    color:#1d4ed8;
                    font-weight:700;
                ">
                    Descuento guardado: $' . number_format($descuentoGuardado, 2) . '
                </div>
            ');
        }

        return null;
    }

    protected function helperSaldoCuenta(): string
    {
        $saldo = (float) $this->record->saldo;
        $descuento = (float) ($this->record->descuento_aplicado ?? 0);

        if ($descuento > 0) {
            return 'Saldo pendiente: $' . number_format($saldo, 2) . ' · Descuento guardado: $' . number_format($descuento, 2);
        }

        return 'Saldo pendiente: $' . number_format($saldo, 2);
    }
}
