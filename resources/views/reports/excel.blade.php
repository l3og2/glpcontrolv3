<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Usuario</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movements as $movement)
            <tr>
                <td>{{ $movement->created_at }}</td>
                <td>{{ $movement->type }}</td>
                <td>{{ $movement->quantity }}</td>
                <td>{{ $movement->user->name ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
