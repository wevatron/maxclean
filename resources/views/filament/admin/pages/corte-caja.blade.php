<x-filament-panels::page>

    <div style="
        max-width: 1100px;
        margin: 0 auto;
        padding: 40px;
    ">

        <div style="
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        ">

            <h2 style="font-size:28px; margin-bottom:30px; color:#111827;">
                Corte de Caja
            </h2>

            <!-- FILTROS -->
            <div style="
                display:flex;
                gap:25px;
                align-items:flex-end;
                flex-wrap:wrap;
                margin-bottom:40px;
            ">

                <div>
                    <label style="display:block; font-size:14px; margin-bottom:8px; color:#374151;">
                        Fecha
                    </label>
                    <input
                        type="date"
                        wire:model="fecha"
                        style="
                            padding:10px 14px;
                            border:1px solid #d1d5db;
                            border-radius:8px;
                            min-width:180px;
                        "
                    >
                </div>

                <div>
                    <label style="display:block; font-size:14px; margin-bottom:8px; color:#374151;">
                        Turno
                    </label>
                    <select
                        wire:model="turno"
                        style="
                            padding:10px 14px;
                            border:1px solid #d1d5db;
                            border-radius:8px;
                            min-width:180px;
                        "
                    >
                        <option value="matutino">Matutino</option>
                        <option value="vespertino">Vespertino</option>
                    </select>
                </div>

                <div style="display:flex; gap:12px;">
                    <button
                        wire:click="cargarPagos"
                        style="
                            padding:10px 18px;
                            border:none;
                            background:#6b7280;
                            color:white;
                            border-radius:8px;
                            cursor:pointer;
                        "
                    >
                        Actualizar
                    </button>

                    <button
                        wire:click="cerrarTurno"
                        style="
                            padding:10px 18px;
                            border:none;
                            background:#16a34a;
                            color:white;
                            border-radius:8px;
                            cursor:pointer;
                        "
                    >
                        Cerrar Turno
                    </button>
                </div>

            </div>

            <!-- RESUMEN -->
            <div style="
                display:grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap:20px;
                margin-bottom:40px;
            ">

                <div style="padding:20px; background:#f3f4f6; border-radius:12px;">
                    <div style="font-size:13px; color:#6b7280;">Total</div>
                    <div style="font-size:24px; font-weight:bold;">
                        ${{ number_format($resumen['total'] ?? 0, 2) }}
                    </div>
                </div>

                <div style="padding:20px; background:#ecfdf5; border-radius:12px;">
                    <div style="font-size:13px; color:#15803d;">Efectivo</div>
                    <div style="font-size:24px; font-weight:bold;">
                        ${{ number_format($resumen['efectivo'] ?? 0, 2) }}
                    </div>
                </div>

                <div style="padding:20px; background:#eff6ff; border-radius:12px;">
                    <div style="font-size:13px; color:#1d4ed8;">Tarjeta</div>
                    <div style="font-size:24px; font-weight:bold;">
                        ${{ number_format($resumen['tarjeta'] ?? 0, 2) }}
                    </div>
                </div>

                <div style="padding:20px; background:#f5f3ff; border-radius:12px;">
                    <div style="font-size:13px; color:#6d28d9;">Transferencia</div>
                    <div style="font-size:24px; font-weight:bold;">
                        ${{ number_format($resumen['transferencia'] ?? 0, 2) }}
                    </div>
                </div>

            </div>

            <!-- TABLA -->
            <div style="
                border:1px solid #e5e7eb;
                border-radius:12px;
                overflow:hidden;
            ">

                <table style="width:100%; border-collapse:collapse;">

                    <thead style="background:#f9fafb;">
                        <tr>
                            <th style="padding:14px; text-align:left; font-size:14px;">ID</th>
                            <th style="padding:14px; text-align:left; font-size:14px;">Método</th>
                            <th style="padding:14px; text-align:left; font-size:14px;">Monto</th>
                            <th style="padding:14px; text-align:left; font-size:14px;">Hora</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pagos as $pago)
                            <tr style="border-top:1px solid #e5e7eb;">
                                <td style="padding:12px;">{{ $pago->id }}</td>
                                <td style="padding:12px;">{{ ucfirst($pago->metodo_pago) }}</td>
                                <td style="padding:12px; font-weight:bold;">
                                    ${{ number_format($pago->monto, 2) }}
                                </td>
                                <td style="padding:12px;">
                                    {{ $pago->created_at->format('H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:25px; text-align:center; color:#9ca3af;">
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