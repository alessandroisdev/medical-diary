<div class="dropdown">
    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Ações
    </button>
    <ul class="dropdown-menu shadow">
        @if($status == 'scheduled' || $status == 'confirmed')
            <li>
                <form action="{{ route('appointments.checkin', $id) }}" method="POST" class="no-ajax m-0 p-0">
                    @csrf
                    <button type="submit" class="dropdown-item text-primary fw-bold"><i class="bi bi-person-check-fill me-2"></i> Realizar Check-In</button>
                </form>
            </li>
            <li><hr class="dropdown-divider"></li>
        @endif

        @if($status == 'arrived' && (Auth::guard('doctor')->check() || Auth::guard('collaborator')->check()))
            <li>
                <button type="button" class="dropdown-item text-warning fw-bold" onclick="callPatientTV('{{ $id }}', '{{ route('attendance.call', $id) }}')">
                    <i class="bi bi-megaphone-fill me-2"></i> Chamar na TV do Saguão
                </button>
            </li>
            <li><hr class="dropdown-divider"></li>
        @endif

        <li><a class="dropdown-item" href="{{ route('appointments.edit', $id) }}"><i class="bi bi-pencil-square me-2"></i> Editar Agendamento</a></li>
        
        <li>
            <form action="{{ route('appointments.destroy', $id) }}" method="POST" class="no-ajax m-0 p-0" onsubmit="return confirm('ATENÇÃO: Deseja apagar este registro do histórico definitivamente? Geralmente marcamos como Cancelado na edição.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Apagar Registro</button>
            </form>
        </li>
    </ul>
</div>
