@forelse($transacciones as $t)
    <tr>
        <td>{{ \Carbon\Carbon::parse($t->fecha)->format('d/m/Y') }}</td>
        <td><span class="font-monospace text-secondary fw-bold">{{ $t->codigo }}</span></td>
        <td>{{ $t->descripcion }}</td>
        <td>
            <span class="badge bg-light text-dark border">{{ $t->categoria->nombre }}</span>
        </td>
        <td class="text-end fw-semibold">Bs. {{ number_format($t->monto, 2) }}</td>
        <td class="text-center">
            @if($t->tipo === 'ingreso')
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Ingreso</span>
            @else
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Egreso</span>
            @endif
        </td>
        <td class="text-end fw-bold {{ $t->saldo_acumulado >= 0 ? 'text-success' : 'text-danger' }}">
            Bs. {{ number_format($t->saldo_acumulado, 2) }}
        </td>
        <td class="text-center">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary btn-edit" data-id="{{ $t->id }}"
                    data-descripcion="{{ $t->descripcion }}" data-monto="{{ $t->monto }}" data-tipo="{{ $t->tipo }}"
                    data-categoria-id="{{ $t->categoria_id }}">
                    Editar
                </button>
                <button type="button" class="btn btn-outline-danger btn-delete" data-id="{{ $t->id }}"
                    data-codigo="{{ $t->codigo }}">
                    Eliminar
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center text-muted py-4">
            No se encontraron transacciones con los filtros seleccionados.
        </td>
    </tr>
@endforelse