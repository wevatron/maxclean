<x-filament-panels::page>

<div style="max-width:1100px; margin:auto; padding:30px;">

    {{-- HEADER --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">

        <div>
            <h1 style="font-size:32px; font-weight:800; color:#ffffff;">
                Ticket #{{ $record->numero }}
            </h1>

            <div style="margin-top:8px; color:#9fb3c8;">
                {{ $record->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <div>
            @if($record->saldo > 0)
                <span style="
                    background:#5a1f1f;
                    color:#ffb4b4;
                    padding:10px 20px;
                    border-radius:999px;
                    font-weight:700;
                ">
                    PENDIENTE
                </span>
            @else
                <span style="
                    background:#1f5a36;
                    color:#b9ffd9;
                    padding:10px 20px;
                    border-radius:999px;
                    font-weight:700;
                ">
                    PAGADO
                </span>
            @endif
        </div>

    </div>

    {{-- INFO GENERAL --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px; margin-bottom:40px;">

        <div style="
            background:#1f446f;
            padding:25px;
            border-radius:16px;
            border:1px solid #2c5d94;
            box-shadow:0 10px 25px rgba(0,0,0,0.35);
        ">
            <h3 style="font-weight:700; margin-bottom:15px; color:#ffffff;">
                Información
            </h3>

            <p style="color:#e6edf5;">
                <strong>Operador:</strong> {{ $record->operador->name }}
            </p>
            <p style="color:#e6edf5;">
                <strong>Sucursal:</strong> {{ $record->sucursal->nombre }}
            </p>
            <p style="color:#e6edf5;">
                <strong>Estado:</strong> {{ $record->status->nombre }}
            </p>
        </div>

        <div style="
            background:#1f446f;
            padding:25px;
            border-radius:16px;
            border:1px solid #2c5d94;
            box-shadow:0 10px 25px rgba(0,0,0,0.35);
        ">
            <h3 style="font-weight:700; margin-bottom:15px; color:#ffffff;">
                Resumen
            </h3>

            <p style="color:#e6edf5;">
                <strong>Total:</strong> ${{ number_format($record->total, 2) }}
            </p>

            <p style="color:#7dffb5;">
                <strong>Pagado:</strong> ${{ number_format($record->pagado, 2) }}
            </p>

            <p style="color:{{ $record->saldo > 0 ? '#ff8a8a' : '#7dffb5' }};">
                <strong>Saldo:</strong> ${{ number_format($record->saldo, 2) }}
            </p>
        </div>

    </div>

    {{-- ITEMS --}}
    <div style="
        background:#1f446f;
        padding:25px;
        border-radius:16px;
        border:1px solid #2c5d94;
        box-shadow:0 10px 25px rgba(0,0,0,0.35);
        margin-bottom:40px;
    ">

        <h3 style="font-weight:700; margin-bottom:20px; color:#ffffff;">
            Prendas
        </h3>

        <table style="width:100%; border-collapse:collapse; color:#e6edf5;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #2c5d94; color:#9fb3c8;">
                    <th>Prenda</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach($record->items as $item)
                    <tr style="border-bottom:1px solid #244d7d;">
                        <td>{{ $item->prenda->nombre }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>${{ number_format($item->precio_unitario, 2) }}</td>
                        <td style="font-weight:600;">
                            ${{ number_format($item->subtotal, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    {{-- PAGOS --}}
    <div style="
        background:#1f446f;
        padding:25px;
        border-radius:16px;
        border:1px solid #2c5d94;
        box-shadow:0 10px 25px rgba(0,0,0,0.35);
    ">

        <h3 style="font-weight:700; margin-bottom:20px; color:#ffffff;">
            Pagos
        </h3>

        @forelse($record->pagos as $pago)
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; color:#e6edf5;">
                <span>{{ ucfirst($pago->metodo_pago) }}</span>
                <span style="font-weight:600;">
                    ${{ number_format($pago->monto, 2) }}
                </span>
            </div>
        @empty
            <div style="color:#9fb3c8;">
                No hay pagos registrados.
            </div>
        @endforelse

    </div>

</div>

</x-filament-panels::page>