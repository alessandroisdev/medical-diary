@extends('layouts.app')

@section('title', 'Totem Senhas - Recepção')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 fw-bold"><i class="bi bi-ticket-perforated-fill text-success me-2"></i>Controle do Totem</h3>
            @if(auth()->guard('collaborator')->user()->current_room)
                <span class="badge bg-success fs-6"><i class="bi bi-geo-alt-fill me-1"></i> Guichê {{ auth()->guard('collaborator')->user()->current_room }}</span>
            @else
                <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> Sem Guichê</span>
            @endif
        </div>

        @if(!auth()->guard('collaborator')->user()->current_room)
            <div class="alert alert-danger shadow-sm border-0">
                <i class="bi bi-info-circle fa-2x me-3"></i>
                <div>
                    Você precisa fazer Check-in em um Guichê no topo da tela antes de poder chamar senhas do Totem.
                </div>
            </div>
        @else

            <!-- Área de Chamada -->
            <div class="card shadow-sm border-0 border-top border-3 border-success mb-4 text-center p-5">
                @if($activeTicket)
                    <h5 class="text-muted text-uppercase tracking-wider">Atendimento Atual</h5>
                    <h1 class="display-1 fw-bold text-dark my-3" id="currentTicketLabel">{{ $activeTicket->number }}</h1>
                    
                    <div class="mt-4">
                        <form id="finishTicketForm" class="mx-auto" style="max-width: 400px;">
                            <div class="mb-3 text-start">
                                <label class="form-label text-muted small">Adicionar Comentário/Anotação (Opcional):</label>
                                <textarea class="form-control" name="comment" rows="2" placeholder="Ex: Paciente agendado com sucesso..."></textarea>
                            </div>
                            <button type="button" class="btn btn-success btn-lg w-100 shadow fw-bold" onclick="finishTicket('{{ $activeTicket->id }}')">
                                <i class="bi bi-check-circle-fill me-2"></i> Finalizar Senha Atual
                            </button>
                        </form>
                    </div>
                @else
                    <i class="bi bi-volume-up text-primary opacity-25" style="font-size: 5rem;"></i>
                    <h3 class="mt-4 text-dark mb-4">Livre para novo atendimento</h3>
                    <button type="button" class="btn btn-primary btn-lg px-5 shadow-lg mx-auto fw-bold fs-4" onclick="callNextTicket()">
                        <i class="bi bi-megaphone-fill me-2"></i> Chamar Próximo da Fila
                    </button>
                @endif
            </div>

        @endif

        <!-- Fila de Espera Info -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-0 fw-bold d-flex justify-content-between align-items-center">
                <span>Senhas Aguardando Hoje</span>
                <span class="badge bg-primary rounded-pill">{{ count($waitingTickets) }}</span>
            </div>
            <div class="card-body p-0">
                @if(count($waitingTickets) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover m-0 align-middle">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-3">Senha</th>
                                    <th>Tipo</th>
                                    <th>Aguardando desde</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($waitingTickets as $ticket)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $ticket->number }}</td>
                                    <td>
                                        @if($ticket->type == 'priority')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Preferencial</span>
                                        @else
                                            <span class="badge bg-secondary">Comum</span>
                                        @endif
                                    </td>
                                    <td class="text-muted"><i class="bi bi-clock me-1"></i> {{ $ticket->created_at->format('H:i') }} ({{ $ticket->created_at->diffForHumans() }})</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-cup-hot fs-1 opacity-50 mb-3 block"></i>
                        <p class="mb-0">Nenhuma senha aguardando no momento.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function callNextTicket() {
        Swal.fire({
            title: 'Calculando a vez...',
            text: 'O algoritmo puxará Próximo pela métrica configurada.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        axios.post('{{ route("reception.queue.call") }}')
            .then(res => {
                showToast('Senha Chamada', 'A TV do saguão foi acionada para ' + res.data.ticket, 'success');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(err => {
                if (err.response && err.response.status === 404) {
                    Swal.fire('Fila Vazia', err.response.data.error, 'info');
                } else {
                    Swal.fire('Erro', err.response?.data?.error || 'Erro interno', 'error');
                }
            });
    }

    function finishTicket(ticketId) {
        const comment = document.querySelector('textarea[name="comment"]').value;
        const btn = document.querySelector('button[onclick^="finishTicket"]');
        const url = '{{ url("reception/tickets") }}/' + ticketId + '/finish';

        if(btn) { btn.disabled = true; btn.innerHTML = 'Aguarde...'; }

        axios.patch(url, { comment: comment })
             .then(res => {
                showToast('Concluído', res.data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
             })
             .catch(e => {
                if(btn) { btn.disabled = false; btn.innerHTML = 'Tentar Novamente'; }
                Swal.fire('Erro', 'Ocorreu um erro ao finalizar senha.', 'error');
             });
    }
</script>
@endpush
