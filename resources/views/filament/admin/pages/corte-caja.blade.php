<x-filament-panels::page>
    <style>
        .corte-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px;
        }

        .corte-card {
            background: #ffffff;
            color: #111827;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
        }

        .corte-title {
            font-size: 28px;
            margin-bottom: 30px;
            color: #111827;
            font-weight: 800;
        }

        .corte-label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
        }

        .corte-input {
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            min-width: 180px;
            background: #ffffff;
            color: #111827;
        }

        .corte-btn {
            padding: 10px 18px;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
        }

        .corte-btn-gray {
            background: #6b7280;
        }

        .corte-btn-green {
            background: #16a34a;
        }

        .corte-summary {
            padding: 20px;
            border-radius: 12px;
            border: 1px solid transparent;
        }

        .corte-summary-total {
            background: #f3f4f6;
        }

        .corte-summary-efectivo {
            background: #ecfdf5;
        }

        .corte-summary-tarjeta {
            background: #eff6ff;
        }

        .corte-summary-transferencia {
            background: #f5f3ff;
        }

        .corte-summary-label {
            font-size: 13px;
            font-weight: 700;
        }

        .corte-summary-value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .corte-table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .corte-table {
            width: 100%;
            border-collapse: collapse;
            color: #111827;
        }

        .corte-table thead {
            background: #f9fafb;
        }

        .corte-table th {
            padding: 14px;
            text-align: left;
            font-size: 14px;
            color: #374151;
        }

        .corte-table td {
            padding: 12px;
            color: #111827;
        }

        .corte-table tr {
            border-top: 1px solid #e5e7eb;
        }

        .corte-empty {
            padding: 25px;
            text-align: center;
            color: #9ca3af !important;
        }

        @media (prefers-color-scheme: dark) {
            .corte-card {
                background: #111827;
                color: #e5e7eb;
                border-color: #1f2937;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            }

            .corte-title {
                color: #ffffff;
            }

            .corte-label {
                color: #cbd5e1;
            }

            .corte-input {
                background: #1f2937;
                border-color: #374151;
                color: #ffffff;
            }

            .corte-input:focus {
                outline: none;
                border-color: #3b82f6;
            }

            .corte-summary {
                border-color: #334155;
            }

            .corte-summary-total {
                background: #1f2937;
            }

            .corte-summary-efectivo {
                background: #052e1b;
            }

            .corte-summary-tarjeta {
                background: #102a43;
            }

            .corte-summary-transferencia {
                background: #2e1065;
            }

            .corte-summary-value {
                color: #ffffff;
            }

            .corte-table-wrap {
                border-color: #334155;
            }

            .corte-table {
                color: #e5e7eb;
            }

            .corte-table thead {
                background: #1f2937;
            }

            .corte-table th {
                color: #cbd5e1;
            }

            .corte-table td {
                color: #e5e7eb;
            }

            .corte-table tr {
                border-top-color: #334155;
            }
        }

        @media (max-width: 768px) {
            .corte-wrap {
                padding: 16px;
            }

            .corte-card {
                padding: 20px;
            }

            .corte-title {
                font-size: 24px;
            }

            .corte-input {
                width: 100%;
            }
        }
    </style>

    <div class="corte-wrap">
        <div class="corte-card">

            <h2 class="corte-title">
                Corte de Caja
            </h2>

            {{-- FILTROS --}}
            <div style="display:flex; gap:25px; align-items:flex-end; flex-wrap:wrap; margin-bottom:40px;">

                <div>
                    <label class="corte-label">
                        Fecha
                    </label>

                    <input type="date" wire:model="fecha" class="corte-input">
                </div>

                <div>
                    <label class="corte-label">
                        Turno
                    </label>

                    <select wire:model="turno" class="corte-input">
                        <option value="matutino">Matutino</option>
                        <option value="vespertino">Vespertino</option>
                    </select>
                </div>

                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <button wire:click="cargarPagos" class="corte-btn corte-btn-gray">
                        Actualizar
                    </button>

                    <button wire:click="confirmarCerrarTurno" class="corte-btn corte-btn-green">
                        Cerrar Turno
                    </button>
                </div>

            </div>

            {{-- RESUMEN --}}
            <div
                style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:40px;">

                <div class="corte-summary corte-summary-total">
                    <div class="corte-summary-label" style="color:#6b7280;">
                        Total
                    </div>
                    <div class="corte-summary-value">
                        ${{ number_format($resumen['total'] ?? 0, 2) }}
                    </div>
                </div>

                <div class="corte-summary corte-summary-efectivo">
                    <div class="corte-summary-label" style="color:#15803d;">
                        Efectivo
                    </div>
                    <div class="corte-summary-value">
                        ${{ number_format($resumen['efectivo'] ?? 0, 2) }}
                    </div>
                </div>

                <div class="corte-summary corte-summary-tarjeta">
                    <div class="corte-summary-label" style="color:#1d4ed8;">
                        Tarjeta
                    </div>
                    <div class="corte-summary-value">
                        ${{ number_format($resumen['tarjeta'] ?? 0, 2) }}
                    </div>
                </div>

                <div class="corte-summary corte-summary-transferencia">
                    <div class="corte-summary-label" style="color:#8b5cf6;">
                        Transferencia
                    </div>
                    <div class="corte-summary-value">
                        ${{ number_format($resumen['transferencia'] ?? 0, 2) }}
                    </div>
                </div>

            </div>

            {{-- TABLA --}}
            <div class="corte-table-wrap">
                <table class="corte-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ticket</th>
                            <th>Modo</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Hora</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pagos as $pago)
                            <tr>
                                <td>{{ $pago->id }}</td>

                                <td>
                                    #{{ str_pad($pago->ticket?->numero, 6, '0', STR_PAD_LEFT) }}
                                </td>

                                <td>
                                    @php
                                        $tipo = $pago->ticket?->tipo;

                                        $modoTexto = match ($tipo) {
                                            'encargo_express' => 'Express',
                                            'encargo_kilo' => 'Por kilo',
                                            default => 'Por encargo',
                                        };

                                        $lavadoTexto = match ($pago->ticket?->tipo_lavado_kilo) {
                                            'basico' => 'Básico',
                                            'premium' => 'Premium',
                                            'extra_lavado' => 'Extra lavado',
                                            default => null,
                                        };
                                    @endphp

                                    <strong>{{ $modoTexto }}</strong>

                                    @if ($tipo === 'encargo_kilo')
                                        <div style="font-size:12px; color:#6b7280;">
                                            {{ $lavadoTexto }} · {{ number_format((float) $pago->ticket?->kilos, 2) }}
                                            kg
                                        </div>
                                    @endif
                                </td>

                                <td>{{ ucfirst($pago->metodo_pago) }}</td>

                                <td style="font-weight:bold;">
                                    ${{ number_format($pago->monto, 2) }}
                                </td>

                                <td>
                                    {{ $pago->created_at->format('H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="corte-empty">
                                    No hay pagos pendientes para este turno.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</x-filament-panels::page>
