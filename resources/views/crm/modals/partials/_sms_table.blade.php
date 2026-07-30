@forelse($gestionesSms as $index => $sms)
    <tr>
        <td class="fw-bold">{{ $gestionesSms->firstItem() + $index }}</td>
        <td>{{ \Carbon\Carbon::parse($sms->fecha)->format('Y-m-d H:i:s') }}</td>
        <td>{{ $sms->telefono }}</td>
        <td>
            <span class="badge {{ $sms->estado == 'ANSWERED' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }}">
                {{ $sms->estado }}
            </span>
        </td>
        <td>{{ $sms->comentario }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-3 text-muted">No hay registros de SMS para este cliente.</td>
    </tr>
@endforelse