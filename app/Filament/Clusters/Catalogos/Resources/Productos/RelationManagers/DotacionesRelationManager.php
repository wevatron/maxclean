<?php

namespace App\Filament\Clusters\Catalogos\Resources\Productos\RelationManagers;

use App\Models\TicketPago;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DotacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'dotaciones';

    protected static ?string $title = 'Dotaciones de inventario';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('cantidad')
                ->label('Cantidad')
                ->numeric()
                ->minValue(1)
                ->required(),

            TextInput::make('precio_compra')
                ->label('Precio de compra')
                ->numeric()
                ->prefix('$')
                ->minValue(0)
                ->required()
                ->default(fn () => (float) ($this->ownerRecord->precio_compra ?? 0)),

            Textarea::make('nota')
                ->label('Nota')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio_compra')
                    ->label('Compra unit.')
                    ->money('MXN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->searchable(),

                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Registrado por')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('imprimir_historial')
                    ->label('Imprimir historial')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (): string => route('productos.dotaciones.pdf', [
                        'producto' => $this->ownerRecord->id,
                    ]))
                    ->openUrlInNewTab(),

                Action::make('agregar_dotacion')
                    ->label('Agregar dotación')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        TextInput::make('precio_compra')
                            ->label('Precio de compra')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->required()
                            ->default(fn () => (float) ($this->ownerRecord->precio_compra ?? 0)),

                        Textarea::make('nota')
                            ->label('Nota')
                            ->rows(3),
                    ])
                    ->action(function (array $data): void {
                        DB::transaction(function () use ($data): void {
                            $cantidad = (int) $data['cantidad'];
                            $precioCompra = (float) $data['precio_compra'];
                            $total = round($cantidad * $precioCompra, 2);
                            $producto = $this->ownerRecord->refresh();

                            $dotacion = $producto->dotaciones()->create([
                                'sucursal_id' => $producto->sucursal_id,
                                'user_id' => Auth::id(),
                                'cantidad' => $cantidad,
                                'precio_compra' => $precioCompra,
                                'total' => $total,
                                'nota' => $data['nota'] ?? null,
                            ]);

                            $pago = TicketPago::create([
                                'ticket_id' => null,
                                'metodo_pago' => 'efectivo',
                                'monto' => $total,
                                'referencia' => 'Dotación de inventario: ' . $producto->nombre,
                                'cancelado' => false,
                                'user_id' => Auth::id(),
                                'sucursal_id' => $producto->sucursal_id,
                                'tipo_movimiento' => 'dotacion',
                                'categoria' => 'inventario',
                                'descripcion' => $data['nota'] ?? 'Dotación de inventario',
                            ]);

                            $dotacion->forceFill([
                                'ticket_pago_id' => $pago->id,
                            ])->save();

                            $producto->increment('existencia', $cantidad);
                            $producto->forceFill([
                                'precio_compra' => $precioCompra,
                            ])->save();
                        });

                        $this->ownerRecord->refresh();

                        Notification::make()
                            ->title('Dotación registrada')
                            ->body('Se agregó inventario y se registró el movimiento de caja.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
