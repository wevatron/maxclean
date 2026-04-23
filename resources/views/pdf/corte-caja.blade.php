<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corte de Caja</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        .totales { margin-top: 20px; }
    </style>
</head>
<body>

<h1>Corte de Caja #{{ $corte->id }}</h1>

<p><strong>Sucursal:</strong> {{ $corte->sucursal->nombre ?? '-' }}</p>
<p><strong>Operador:</strong> {{ $corte->operador->name ?? '-' }}</p>
<p><strong>Fecha:</strong> {{ $corte->fecha->format('d/m/Y') }}</p>
<p><strong>Turno:</strong> {{ ucfirst($corte->turno) }}</p>
<p><strong>Cerrado:</strong> {{ $corte->cerrado_en->format('d/m/Y H:i') }}</p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Método</th>
            <th>Monto</th>
            <th>Hora</th>
        </tr>
    </thead>
    <tbody>
        @foreach($corte->pagos as $pago)
            <tr>
                <td>{{ $pago->id }}</td>
                <td>{{ ucfirst($pago->metodo_pago) }}</td>
                <td>${{ number_format($pago->monto, 2) }}</td>
                <td>{{ $pago->created_at->format('H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="totales">
    <p><strong>Total:</strong> ${{ number_format($corte->total, 2) }}</p>
    <p><strong>Efectivo:</strong> ${{ number_format($corte->total_efectivo, 2) }}</p>
    <p><strong>Tarjeta:</strong> ${{ number_format($corte->total_tarjeta, 2) }}</p>
    <p><strong>Transferencia:</strong> ${{ number_format($corte->total_transferencia, 2) }}</p>
</div>

</body>
</html>