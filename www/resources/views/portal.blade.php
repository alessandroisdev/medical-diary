@extends('layouts.app')

@section('title', 'Self-Booking Motor - Área do Paciente')

@section('content')
<div class="row mt-4">
    <div class="col-12 col-xl-10 mx-auto">
        <h3 class="fw-bold text-dark mb-4"><i class="bi bi-calendar2-check-fill text-primary me-2"></i> Agende sua Nova Consulta</h3>

        <div class="card shadow-sm border-0 border-top border-primary border-4 mb-5">
            <div class="card-body p-5">
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
                            <!-- Input Hidden para o form segurar o tempo -->
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
</div>

@push('scripts')
<style>
    .slot-btn { transition: all 0.2s; }
    .min-h-120 { min-height: 120px; }
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
        // Validar nativo
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
                setTimeout(() => window.location.href = data.redirect, 2500);
            }
        } catch(e) {
            let msg = e.response?.data?.message || 'Erro ao sincronizar.';
            showToast('Alerta', msg, 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Confirmar Agendamento';
        }
    });
});
</script>
@endpush
@endsection
