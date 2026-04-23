@extends('layouts.app')

@section('title', 'Meu Hub - Área do Paciente')

@section('content')
<div class="row mt-4">
    <div class="col-12 col-xl-10 mx-auto">
        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-person-bounding-box text-primary me-2"></i> Portal do Paciente</h3>
            <span class="badge bg-secondary ms-3">Sessão Segura</span>
        </div>

        <ul class="nav nav-pills mb-4 nav-fill shadow-sm rounded bg-white p-2" id="portalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-uppercase px-4" id="agendar-tab" data-bs-toggle="pill" data-bs-target="#agendar" type="button" role="tab"><i class="bi bi-calendar-plus me-1"></i> Nova Consulta</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-uppercase px-4" id="historico-tab" data-bs-toggle="pill" data-bs-target="#historico" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Meu Histórico</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-uppercase px-4 text-success" id="receitas-tab" data-bs-toggle="pill" data-bs-target="#receitas" type="button" role="tab"><i class="bi bi-file-earmark-medical me-1"></i> Minhas Receitas</button>
            </li>
        </ul>

        <div class="tab-content" id="portalTabsContent">
            
            <!-- ABA 1: Agendar Consulta -->
            <div class="tab-pane fade show active" id="agendar" role="tabpanel">
                <div class="card shadow-sm border-0 border-top border-primary border-4 mb-5">
                    <div class="card-body p-4 p-md-5">
                        <form id="bookingEngineForm" class="no-ajax">
                            <!-- FASE 1: Especialidade e Médico -->
                            <div class="row g-4 mb-4" id="step1">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary">Especialidade Desejada</label>
                                    <select class="form-select form-select-lg live-search" id="selSpecialty" name="specialty_id" required>
                                        <option value="" selected disabled>Busque a especialidade clínica...</option>
                                        @foreach($specialties as $sp)
                                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary">Médico Especialista</label>
                                    <select class="form-select form-select-lg" id="selDoctor" name="doctor_id" disabled required>
                                        <option value="" selected disabled>Aguardando especialidade...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- FASE 2: Data e Slots Magnéticos -->
                            <div class="row g-4 mb-4 d-none" id="step2">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary">Escolha a Data</label>
                                    <input type="date" class="form-control form-control-lg" id="inpDate" name="date" min="{{ date('Y-m-d') }}" required>
                                    <div class="form-text">Verificaremos a disponibilidade em tempo real.</div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-secondary">Horários Disponíveis</label>
                                    <div id="slotsContainer" class="d-flex flex-wrap gap-2 pt-1 border rounded p-3 bg-light min-h-120">
                                        <span class="text-muted w-100 text-center py-4"><i class="bi bi-arrow-left-circle me-1"></i> Selecione a Data Mapeada</span>
                                    </div>
                                    <input type="hidden" name="time" id="inpTime" required>
                                </div>
                            </div>

                            <!-- FASE 3: Finanças & Confirmação -->
                            <div class="row g-4 d-none bg-info bg-opacity-10 p-4 rounded mt-2 border border-info" id="step3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-dark fs-5"><i class="bi bi-wallet2 me-2"></i>Método de Faturamento / Cobertura</label>
                                    <select class="form-select form-select-lg" id="selPayment" name="payment_method" required>
                                        <option value="" selected disabled>Calculando coberturas da clínica...</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary btn-lg w-100 fw-bold shadow" id="btnConfirmBook">
                                        <i class="bi bi-check-circle me-2"></i> Confirmar Agendamento
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ABA 2: Histórico Clínico -->
            <div class="tab-pane fade" id="historico" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @if($appointments->isEmpty())
                            <div class="text-center p-5 text-muted">
                                <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                                <h5>Nenhuma consulta encontrada.</h5>
                                <p>Sua jornada de saúde começa aqui. Agende sua primeira consulta na aba correspondente.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush rounded">
                                @foreach($appointments as $app)
                                    @php
                                        $badgeClass = match($app->status) {
                                            'scheduled' => 'bg-primary',
                                            'arrived' => 'bg-info text-dark',
                                            'in_progress' => 'bg-warning text-dark',
                                            'completed' => 'bg-success',
                                            'canceled', 'no_show' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        $statusFormat = match($app->status) {
                                            'scheduled' => 'Agendado',
                                            'arrived' => 'Aguardando na Fila',
                                            'in_progress' => 'Em Atendimento',
                                            'completed' => 'Finalizado',
                                            'canceled' => 'Cancelado',
                                            'no_show' => 'Faltou',
                                            default => 'Desconhecido'
                                        };
                                        $specs = $app->doctor->specialties->pluck('name')->implode(', ');
                                    @endphp
                                    <div class="list-group-item p-4 border-bottom">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <h5 class="fw-bold mb-1 text-dark">{{ \Carbon\Carbon::parse($app->scheduled_at)->format('d/m/Y') }}</h5>
                                                <span class="text-muted"><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($app->scheduled_at)->format('H:i') }}</span>
                                            </div>
                                            <div class="col-md-5">
                                                <h6 class="fw-bold mb-1"><i class="bi bi-person-fill me-1"></i> Dr(a). {{ $app->doctor->name }}</h6>
                                                <span class="text-secondary small">{{ $specs }}</span>
                                            </div>
                                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                                <span class="badge {{ $badgeClass }} fs-6 shadow-sm"><i class="bi bi-record-circle me-1"></i>{{ $statusFormat }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ABA 3: Fichário de Receitas -->
            <div class="tab-pane fade" id="receitas" role="tabpanel">
                <div class="card border-0 shadow-sm border-top border-success border-4">
                    <div class="card-body p-4 bg-light text-center border-bottom">
                        <i class="bi bi-shield-check text-success fs-1 mb-2"></i>
                        <h5 class="fw-bold mb-0">Ambiente Criptografado</h5>
                        <p class="text-muted small mb-0">Nesta aba você tem acesso oficial às suas prescrições em PDF homologadas com rastreio protegido.</p>
                    </div>
                    <div class="card-body p-0">
                        @if($prescriptions->isEmpty())
                            <div class="text-center p-5 text-muted">
                                <h5>Nenhuma receita emitida até o momento.</h5>
                                <p>Os médicos enviarão seus tratamentos digitalmente na sua próxima visita.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Data da Emissão</th>
                                            <th>Médico Prescritor</th>
                                            <th>Identificador Lógico</th>
                                            <th class="text-end pe-4">Ação Documental</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prescriptions as $recipe)
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">{{ \Carbon\Carbon::parse($recipe->created_at)->format('d/m/Y H:i') }}</td>
                                                <td><i class="bi bi-person text-secondary"></i> Dr(a). {{ $recipe->doctor->name ?? 'Desconhecido' }}</td>
                                                <td><span class="badge bg-light text-dark border font-monospace">RCPT-{{ substr($recipe->id, 0, 8) }}</span></td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('portal.prescription.download', $recipe->id) }}" target="_blank" class="btn btn-sm btn-success shadow-sm fw-bold">
                                                        <i class="bi bi-cloud-arrow-down me-1"></i> Baixar e Imprimir
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div> <!-- /.tab-content -->
    </div>
</div>

@push('scripts')
<style>
    .slot-btn { transition: all 0.2s; }
    .min-h-120 { min-height: 120px; }
    .nav-pills .nav-link { color: #495057; border-radius: 8px; margin: 0 4px; }
    .nav-pills .nav-link.active { background-color: #0d6efd; color: #fff !important; box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.2); }
    .nav-pills .nav-link#receitas-tab.active { background-color: #198754; box-shadow: 0 4px 6px -1px rgba(25, 135, 84, 0.2); }
</style>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('.live-search', { create: false, sortField: { field: "text", direction: "asc" } });

    const selSpecialty = document.getElementById('selSpecialty');
    const selDoctor = document.getElementById('selDoctor');
    const inpDate = document.getElementById('inpDate');
    const slotsContainer = document.getElementById('slotsContainer');
    const inpTime = document.getElementById('inpTime');
    const selPayment = document.getElementById('selPayment');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');

    // 1. Mudança na Especialidade carrega Médicos
    selSpecialty.addEventListener('change', async function() {
        if(!this.value) return;
        step2.classList.add('d-none');
        step3.classList.add('d-none');
        selDoctor.innerHTML = '<option value="" disabled selected>Buscando Médicos...</option>';
        selDoctor.disabled = true;

        try {
            const { data } = await axios.get('/api/portal/doctors', { params: { specialty_id: this.value } });
            if(data.length === 0) {
                selDoctor.innerHTML = '<option value="" disabled selected>Nenhum médico atende essa especialidade.</option>';
            } else {
                selDoctor.innerHTML = '<option value="" disabled selected>Selecione quem irá te atender...</option>';
                data.forEach(doc => {
                    selDoctor.innerHTML += `<option value="${doc.id}">${doc.name}</option>`;
                });
                selDoctor.disabled = false;
            }
        } catch(e) { console.error(e); }
    });

    // 2. Mudança no Médico carrega Pagamentos e Libera o Date
    selDoctor.addEventListener('change', async function() {
        if(!this.value) return;
        step2.classList.remove('d-none');
        step3.classList.add('d-none');
        inpDate.value = '';
        slotsContainer.innerHTML = '<span class="text-muted w-100 text-center py-4"><i class="bi bi-arrow-left-circle me-1"></i> Selecione a Data Mapeada</span>';
        
        // Carrega Pagamentos
        selPayment.innerHTML = '<option value="" disabled selected>Calculando...</option>';
        try {
            const { data } = await axios.get('/api/portal/payment-methods', { params: { doctor_id: this.value } });
            selPayment.innerHTML = '<option value="" disabled selected>Como deseja custear a consulta?</option>';
            data.forEach(m => {
                selPayment.innerHTML += `<option value="${m.id}">${m.name} (Acordo: R$ ${m.price})</option>`;
            });
        } catch(e) {}
    });

    // 3. Mudança na Data busca Slots Magnéticos
    inpDate.addEventListener('change', async function() {
        if(!this.value) return;
        step3.classList.add('d-none');
        inpTime.value = '';
        slotsContainer.innerHTML = '<div class="spinner-border text-primary mx-auto" role="status"></div>';
        
        try {
            const { data } = await axios.get('/api/portal/slots', { 
                params: { doctor_id: selDoctor.value, specialty_id: selSpecialty.value, date: this.value } 
            });
            slotsContainer.innerHTML = '';
            
            if(data.slots.length === 0) {
                slotsContainer.innerHTML = '<span class="text-danger w-100 text-center fw-bold py-4">Agenda Cheia ou Sem Atendimento neste dia. Tente outra data.</span>';
                return;
            }

            data.slots.forEach(time => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-primary fw-bold slot-btn';
                btn.innerHTML = `<i class="bi bi-clock"></i> ${time}`;
                btn.onclick = function() {
                    document.querySelectorAll('.slot-btn').forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
                    document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('text-white'));
                    this.classList.replace('btn-outline-primary', 'btn-primary');
                    this.classList.add('text-white');
                    inpTime.value = time;
                    step3.classList.remove('d-none');
                };
                slotsContainer.appendChild(btn);
            });
        } catch(e) {
            slotsContainer.innerHTML = '<span class="text-danger">Erro na sincronização de horários matemáticos.</span>';
        }
    });

    // 4. Submit para Criar o Appointment
    document.getElementById('btnConfirmBook').addEventListener('click', async function() {
        const form = document.getElementById('bookingEngineForm');
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processando Blockchain...';

        try {
            const formData = new FormData(form);
            const { data } = await axios.post('/api/portal/book', formData);
            if(data.success) {
                showToast('Agendamento', data.message, 'success');
                setTimeout(() => window.location.reload(), 2500); // Reload pra ver no historico
            }
        } catch(e) {
            let msg = e.response?.data?.message || 'Erro ao sincronizar.';
            showToast('Alerta', msg, 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Confirmar Agendamento';
        }
    });

    // Script to deal with Bootstrap Tab activation from URL ?tab= option if we wanted
});
</script>
@endpush
@endsection
