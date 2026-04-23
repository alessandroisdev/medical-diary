@extends('layouts.app')

@section('title', 'Painel de Agendas e Escalas Médicas - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-calendar-check me-2"></i>Escala Diária de Corpos Clínicos</h4>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-light">
                <form action="{{ route('schedules.index') }}" method="GET" class="d-flex gx-3 align-items-end w-50">
                    <div class="flex-grow-1 me-3">
                        <label class="form-label fw-bold">Data Alvo de Gestão:</label>
                        <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Médicos e Status de Escala -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Médico(a) Responsável</th>
                                <th>CRM</th>
                                <th class="text-center">Status no Dia ({{ \Carbon\Carbon::parse($date)->format('d/m/Y') }})</th>
                                <th class="text-end">Painel de Ações Fixas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctors as $d)
                                @php
                                    $hasConfig = $schedules->get($d->id);
                                    $statusBtn = 'success';
                                    $statusDesc = 'Atendimento Normal (Ativo)';
                                    $icon = 'bi-check-circle-fill';
                                    
                                    if($hasConfig) {
                                        if($hasConfig->status === 'cancelled') {
                                            $statusBtn = 'danger';
                                            $statusDesc = 'Afastado/Ausência - ' . ($hasConfig->reason ?? 'Não justificado');
                                            $icon = 'bi-x-circle-fill';
                                        } elseif($hasConfig->status === 'vacation') {
                                            $statusBtn = 'warning';
                                            $statusDesc = 'Férias Programadas';
                                            $icon = 'bi-cup-hot-fill';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="fw-bold">Dr(a). {{ $d->name }}</td>
                                    <td>{{ $d->crm ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusBtn }} p-2" style="font-size: 0.9em;">
                                            <i class="bi {{ $icon }} me-1"></i> {{ $statusDesc }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group border rounded shadow-sm">
                                            <!-- Action: Activate -->
                                            <button type="button" class="btn btn-sm btn-light text-success fw-bold p-2" onclick="toggleSchedule('{{ $d->id }}', 'active')" title="Marcar como Dia Ativo">
                                                <i class="bi bi-play-circle"></i> Liberar Atuação
                                            </button>

                                            <!-- Action: Block / Cancel -->
                                            <button type="button" class="btn btn-sm btn-light border-start border-end text-danger fw-bold p-2" onclick="promptBlock('{{ $d->id }}', 'cancelled')" title="Bloquear Agenda por Imprevisto">
                                                <i class="bi bi-ban"></i> Cancelar Dia
                                            </button>

                                            <!-- Action: Vacation -->
                                            <button type="button" class="btn btn-sm btn-light text-warning text-dark fw-bold p-2" onclick="toggleSchedule('{{ $d->id }}', 'vacation')" title="Lançar Férias">
                                                <i class="bi bi-cup-hot"></i> Férias
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($doctors->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-4">Nenhum médico operando base cadastrado ainda.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal oculto pra forçar Axios silencioso com motivos -->
<form id="hiddenEngine" method="POST" style="display:none;" class="no-redirect-on-success">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="status" id="statusInput">
    <input type="hidden" name="reason" id="reasonInput" value="">
</form>

@endsection

@push('scripts')
<script>
    function toggleSchedule(doctorId, status) {
        if(!confirm('Confirma a mudança de estado global do médico nesta data?')) return;
        
        execXhr(doctorId, status, '');
    }

    function promptBlock(doctorId, status) {
        const reason = prompt('Qual o motivo do cancelamento repentino (ficará visível pro call center)?');
        if(reason === null) return; // Cancelado
        
        execXhr(doctorId, status, reason);
    }

    function execXhr(doctorId, status, reason) {
        const form = document.getElementById('hiddenEngine');
        form.action = `/schedules/${doctorId}/toggle`;
        document.getElementById('statusInput').value = status;
        document.getElementById('reasonInput').value = reason;

        // Anexa listener temporário no app.ts axio stack ou recarrega direto
        // O engine global em `app.ts` processa Form submissão automática. 
        // Despachamos evento nativo:
        const submitEvent = new SubmitEvent('submit', { cancelable: true, bubbles: true });
        
        // Custom hook para recarregar a visualização pós-response do axios (já que é tela interativa SPA-like)
        form.addEventListener('submit', function onSub() {
            setTimeout(() => { window.location.reload(); }, 500);
            form.removeEventListener('submit', onSub);
        }, {once: true});

        form.dispatchEvent(submitEvent);
    }
</script>
@endpush
