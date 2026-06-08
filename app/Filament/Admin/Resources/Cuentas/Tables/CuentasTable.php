<?php

namespace App\Filament\Admin\Resources\Cuentas\Tables;

use App\Filament\Admin\Resources\Cuentas\CuentaResource;
use App\Models\Cuenta;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CuentasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['cliente', 'sucursal', 'operador'])
                    ->withCount('tickets')
                    ->latest('id');
            })
            ->columns([
                TextColumn::make('cliente.name')
                    ->label('Cliente')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('cuenta_card')
                    ->label('Cuentas')
                    ->state(fn (Cuenta $record): HtmlString => self::renderCuentaCard($record))
                    ->html()
                    ->searchable(false),
            ])
            ->filters([
                SelectFilter::make('estatus')
                    ->label('Estatus')
                    ->options([
                        'abierta' => 'Abierta',
                        'parcial' => 'Parcial',
                        'pagada' => 'Pagada',
                        'cancelada' => 'Cancelada',
                    ]),

                Filter::make('cuentas_pendientes')
                    ->label('Con saldo pendiente')
                    ->query(fn (Builder $query) => $query->where('saldo', '>', 0)),

                Filter::make('hoy')
                    ->label('Abiertas hoy')
                    ->query(fn (Builder $query) => $query->whereDate('abierta_en', now()->toDateString())),
            ])
            ->recordActions([])
            ->recordUrl(fn (Cuenta $record): string => CuentaResource::getUrl('edit', [
                'record' => $record,
            ]));
    }

    private static function renderCuentaCard(Cuenta $record): HtmlString
    {
        $estatus = $record->estatus ?? 'sin_estatus';

        $estatusLabel = match ($estatus) {
            'abierta' => 'Abierta',
            'parcial' => 'Parcial',
            'pagada' => 'Pagada',
            'cancelada' => 'Cancelada',
            default => 'Sin estatus',
        };

        $estatusStyle = match ($estatus) {
            'abierta' => 'background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;',
            'parcial' => 'background:#fef3c7;color:#92400e;border:1px solid #fde68a;',
            'pagada' => 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;',
            'cancelada' => 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;',
            default => 'background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;',
        };

        $saldoStyle = (float) $record->saldo > 0
            ? 'color:#b45309;background:#fffbeb;border:1px solid #fde68a;'
            : 'color:#166534;background:#f0fdf4;border:1px solid #bbf7d0;';

        $numero = e($record->numero ?? 'Sin número');
        $cliente = e($record->cliente?->name ?? 'Sin cliente');
        $sucursal = e($record->sucursal?->nombre ?? 'Sin sucursal');
        $operador = e($record->operador?->name ?? 'Sin operador');

        $total = '$' . number_format((float) $record->total, 2);
        $pagado = '$' . number_format((float) $record->total_pagado, 2);
        $saldo = '$' . number_format((float) $record->saldo, 2);

        $abierta = $record->abierta_en
            ? $record->abierta_en->format('d/m/Y H:i')
            : ($record->created_at ? $record->created_at->format('d/m/Y H:i') : '—');

        $cerrada = $record->cerrada_en
            ? $record->cerrada_en->format('d/m/Y H:i')
            : '—';

        $tickets = (int) ($record->tickets_count ?? 0);

        $html = <<<HTML
<style>
    .mc-cuenta-card {
        transition: all .18s ease;
    }

    .mc-cuenta-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 26px rgba(15,23,42,.12) !important;
        border-color: #bfdbfe !important;
    }

    @media (max-width: 900px) {
        .mc-cuenta-card-grid {
            grid-template-columns: repeat(2, minmax(120px, 1fr)) !important;
        }

        .mc-cuenta-card-info {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="mc-cuenta-card" style="
    width:100%;
    padding:18px;
    border-radius:18px;
    background:linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border:1px solid #e5e7eb;
    box-shadow:0 8px 20px rgba(15,23,42,.06);
    cursor:pointer;
">
    <div style="
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:16px;
        flex-wrap:wrap;
        margin-bottom:14px;
    ">
        <div>
            <div style="font-size:20px;font-weight:800;color:#111827;line-height:1.1;">
                Cuenta {$numero}
            </div>
            <div style="font-size:16px;color:#0f172a;margin-top:6px;font-weight:800;line-height:1.2;">
                {$cliente}
            </div>
        </div>

        <div style="
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:800;
            {$estatusStyle}
        ">
            {$estatusLabel}
        </div>
    </div>

    <div class="mc-cuenta-card-grid" style="
        display:grid;
        grid-template-columns:repeat(4, minmax(120px, 1fr));
        gap:10px;
        margin-bottom:14px;
    ">
        <div style="padding:10px;border-radius:14px;background:#f9fafb;border:1px solid #edf2f7;">
            <div style="font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;">Total</div>
            <div style="font-size:18px;font-weight:800;color:#111827;">{$total}</div>
        </div>

        <div style="padding:10px;border-radius:14px;background:#f0fdf4;border:1px solid #bbf7d0;">
            <div style="font-size:11px;color:#166534;font-weight:700;text-transform:uppercase;">Pagado</div>
            <div style="font-size:18px;font-weight:800;color:#166534;">{$pagado}</div>
        </div>

        <div style="padding:10px;border-radius:14px;{$saldoStyle}">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;">Saldo</div>
            <div style="font-size:18px;font-weight:800;">{$saldo}</div>
        </div>

        <div style="padding:10px;border-radius:14px;background:#eff6ff;border:1px solid #bfdbfe;">
            <div style="font-size:11px;color:#1d4ed8;font-weight:700;text-transform:uppercase;">Tickets</div>
            <div style="font-size:18px;font-weight:800;color:#1d4ed8;">{$tickets}</div>
        </div>
    </div>

    <div class="mc-cuenta-card-info" style="
        display:grid;
        grid-template-columns:repeat(2, minmax(160px, 1fr));
        gap:8px 16px;
        font-size:13px;
        color:#374151;
    ">
        <div>
            <span style="font-weight:800;color:#111827;">Sucursal:</span> {$sucursal}
        </div>
        <div>
            <span style="font-weight:800;color:#111827;">Operador:</span> {$operador}
        </div>
        <div>
            <span style="font-weight:800;color:#111827;">Abierta:</span> {$abierta}
        </div>
        <div>
            <span style="font-weight:800;color:#111827;">Cerrada:</span> {$cerrada}
        </div>
    </div>

    <div style="
        margin-top:14px;
        padding-top:12px;
        border-top:1px solid #e5e7eb;
        font-size:12px;
        font-weight:700;
        color:#2563eb;
    ">
        Click para gestionar cuenta
    </div>
</div>
HTML;

        return new HtmlString($html);
    }
}
