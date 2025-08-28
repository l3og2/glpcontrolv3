<table>
    <thead>
        <tr>
            <th>N° Control</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Volumen (Lts)</th>
            <th>Cantidad</th>
            <th>Precio Unitario (Bs.)</th>
            <th>Monto Total (Bs.)</th>
            <th>Estado</th>
            <th>Registrado Por</th>
            <th>Región</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movements as $movement)
            <tr>
                <td>{{ $movement->control_number }}</td>
                <td>{{ $movement->movement_date->format('d/m/Y H:i') }}</td>
                <td>{{ ucfirst($movement->type) }}</td>
                <td>{{ $movement->type == 'entrada' ? ($movement->tank->name_location ?? 'N/A') : ($movement->product->name ?? 'N/A') }}</td>
                <td>{{ $movement->volume_liters }}</td>
                <td>{{ $movement->quantity }}</td>
                <td>{{ $movement->unit_price }}</td>
                <td>{{ $movement->total_amount }}</td>
                <td>{{ ucfirst($movement->status) }}</td>
                <td>{{ $movement->user->name ?? 'N/A' }}</td>
                <td>{{ $movement->state->name ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>