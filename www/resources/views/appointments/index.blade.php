@extends('layouts.app')

@section('title', 'Recepção - Gestão de Agendamentos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-calendar-check me-2"></i>Recepção: Painel de Agendamentos</h4>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-calendar2-plus me-1"></i> Agendamento Manual / Encaixe
            </a>
        </div>

        <div class="alert alert-primary shadow-sm border-0 d-flex align-items-center mb-4">
            <i class="bi bi-info-circle fa-2x me-3"></i>
            <div>
                Aqui você visualiza toda a fila da clínica de forma inteligente. O grid prioriza nativamente os pacientes que já <b class="text-danger">Chegaram</b> hoje para focar no fluxo do balcão. Use a coluna de <b>Ações</b> para despachar pra TV.
            </div>
        </div>

        <!-- Painel de Filtros Avançados -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="card-title fw-bold text-muted mb-3"><i class="bi bi-funnel me-1"></i> Filtros e Live Search</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Por Dia</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status do Paciente</label>
                        <select class="form-select" id="status_filter">
                            <option value="">Exibir Todos (Ordem de Relevância)</option>
                            <option value="scheduled">Agendados Futuros</option>
                            <option value="confirmed">Confirmados Via Site</option>
                            <option value="arrived">Aguardando no Balcão</option>
                            <option value="in_consultation">Dentro do Consultório</option>
                            <option value="finished">Já Atendidos (Finalizados)</option>
                            <option value="canceled">Cancelados Manuais</option>
                            <option value="no_show">Pacientes que Faltaram</option>
                        </select>
                    </div>
                    
                    @if(!auth()->guard('doctor')->check())
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Filtro de Médico <span class="badge bg-secondary ms-1" style="font-size: 0.6rem;">Search</span></label>
                        <select class="form-select ts-filter" id="doctor_filter" placeholder="Escreva pra pesquisar...">
                            <option value="">Qualquer Médico</option>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} @if($d->specialty) - {{ $d->specialty->name }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Filtro de Paciente <span class="badge bg-secondary ms-1" style="font-size: 0.6rem;">Search</span></label>
                        <select class="form-select ts-filter" id="client_filter" placeholder="Escreva CPF ou Nome...">
                            <option value="">Pesquisar em Toda Base</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->cpf ?? 'Sem Doc' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm border-top border-primary border-3">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-bordered table-striped w-100 align-middle']) !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Tom Select Assets -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    {!! $dataTable->scripts() !!}
    <script>
        const loggedDocRoom = {!! Auth::guard('doctor')->check() && Auth::guard('doctor')->user()->current_room ? "'" . Auth::guard('doctor')->user()->current_room . "'" : 'null'  !!};
        const isDoctor = {{ Auth::guard('doctor')->check() ? 'true' : 'false' }};

        // 1. Inicializa TomSelect (Live Search Elegante)
        document.querySelectorAll('.ts-filter').forEach((el) => {
            new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: el.getAttribute('placeholder')
            });
        });

        // 2. Observer de Filtros -> Dispara Reload na Tabela (Enviando pro Backend a Query)
        $('#date_filter, #status_filter, #doctor_filter, #client_filter').on('change', function() {
            window.LaravelDataTables["appointments-table"].ajax.reload();
        });

        // ==========================================
        // TV Caller Functions
        // ==========================================
        function submitCall(url, roomVal) {
            Swal.fire({
                title: 'Anunciando na TV...',
                text: 'Disparando no Totem da recepção.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            axios.post(url, { room: roomVal })
                .then(response => {
                    Swal.fire('Chamado!', response.data.message, 'success');
                    window.LaravelDataTables["appointments-table"].ajax.reload();
                })
                .catch(error => {
                    Swal.fire('Erro!', 'Não foi possível contatar o Totem.', 'error');
                });
        }

        function callPatientTV(id, url) {
            if(isDoctor) {
                if(!loggedDocRoom) {
                    Swal.fire('Sem Sala Associada', 'Você precisa informar via Botão Superior (no Topo do seu Painel) qual o seu consultório do dia para utilizar a Chamada Expressa 1-Click!', 'warning');
                    return;
                }
                // 1-Click Direto
                submitCall(url, loggedDocRoom);
            } else {
                // Recepção acionando, pede a sala
                Swal.fire({
                    title: 'Chamar na TV',
                    input: 'text',
                    inputLabel: 'Para qual Sala/Consultório o paciente deve se direcionar?',
                    inputPlaceholder: 'Ex: Sala de Triagem',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Chamar Agora',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => {
                        if (!value) return 'Informe a sala!'
                    }
                }).then((result) => {
                    if (result.isConfirmed) submitCall(url, result.value);
                });
            }
        }
    </script>
@endpush
