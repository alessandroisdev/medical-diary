@extends('layouts.app')

@section('title', 'Novo Prontuário Clínico - Medical Diary')

@section('content')
<div class="row mb-5">
    <div class="col-12 col-xl-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-file-earmark-medical me-2"></i>Novo Atendimento Médico</h4>
            <a href="{{ route('records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Lista
            </a>
        </div>

        <div class="card border-0 shadow-lg border-top border-danger border-3">
            <form action="{{ route('records.store') }}" method="POST" id="recordForm">
                @csrf
                <div class="card-body bg-light p-4">
                    <!-- Referência Cruzada -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Consulta Ativa (Painel)</label>
                            <select name="appointment_id" class="form-select border-danger">
                                <option value="">Atendimento Avulso (Sem Agendamento prévio)</option>
                                @foreach($appointments as $app)
                                    <option value="{{ $app->id }}" data-client-id="{{ $app->client_id }}" data-doctor-id="{{ $app->doctor_id }}">Sala de Espera: {{ $app->client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Selecione o Paciente</label>
                            <select name="client_id" class="form-select live-search" required>
                                <option value="">Busca Rápida...</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} (CPF: {{ $c->cpf ?? 'S/N' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Médico Responsável</label>
                            <select name="doctor_id" class="form-select live-search" required>
                                <option value="">Assinatura...</option>
                                @foreach($doctors as $d)
                                    <option value="{{ $d->id }}" @selected(Auth::guard('doctor')->id() == $d->id)>Dr(a). {{ $d->name }} (CRM: {{ $d->crm }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Abas internas -->
                    <ul class="nav nav-pills mb-3" id="recordTabs" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#anamnesis" type="button" role="tab"><i class="bi bi-clipboard-pulse me-1"></i>Anamnese & Exame Físico</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#diagnosis" type="button" role="tab"><i class="bi bi-heart-pulse me-1"></i>Diagnóstico/Conduta</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-success" data-bs-toggle="pill" data-bs-target="#prescription" type="button" role="tab"><i class="bi bi-capsule me-1"></i>Gerar Receituário Múltiplo</button>
                      </li>
                    </ul>

                    <div class="tab-content border bg-white p-4 rounded shadow-sm" id="recordTabsContent">
                        
                        <!-- TAB EXAME -->
                        <div class="tab-pane fade show active" id="anamnesis" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-primary">Sintomatologia e Queixa Principal</label>
                                <textarea name="symptoms" class="form-control" rows="8" placeholder="Relato inicial do paciente..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Anotações Gerais Adicionais</label>
                                <textarea name="notes" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <!-- TAB DIAGNOSTICO -->
                        <div class="tab-pane fade" id="diagnosis" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-danger">Diagnóstico Clínico Específico (CID Opcional)</label>
                                <input type="text" name="diagnosis" class="form-control form-control-lg border-danger" required placeholder="Ex: J03.9 Amigdalite aguda não especificada">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Plano Terapêutico/Conduta Adotada</label>
                                <textarea name="treatment_plan" class="form-control" rows="8" required placeholder="Instruções de conduta de longo prazo e restrições..."></textarea>
                            </div>
                        </div>

                        <!-- TAB RECEITA -->
                        <div class="tab-pane fade" id="prescription" role="tabpanel">
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" role="switch" name="has_prescription" id="hasPrescriptionSwitch" value="1">
                                <label class="form-check-label fw-bold" for="hasPrescriptionSwitch">Emitir Assinatura Digital e Receituário Farmacêutico?</label>
                            </div>

                            <div id="prescriptionForm" style="display: none; border-left: 4px solid #198754; padding-left: 15px;">
                                <div class="mb-3">
                                    <label class="form-label text-success fw-bold">Fármacos e Medicamentos (Lista)</label>
                                    <div id="medList">
                                        <div class="input-group mb-2 med-item">
                                            <input type="text" name="medicines[]" class="form-control" placeholder="1. Amoxicilina 500mg - 1 Caixa">
                                            <button type="button" class="btn btn-outline-danger btn-remove-med"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success mt-1" id="addMedBtn"><i class="bi bi-plus"></i> Adicionar Remédio</button>
                                </div>
                                <div class="mb-3 mt-4">
                                    <label class="form-label text-success fw-bold">Posologia (Orientações de Uso)</label>
                                    <textarea name="instructions" class="form-control" rows="5" placeholder="Tomar 1 comp a cada 8h..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top p-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger btn-lg px-5 shadow-sm fw-bold" data-original-text="Assinar Digitalmente e Salvar Prontuário">Assinar Digitalmente e Salvar Prontuário</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Logica Dinâmica Receituario
            const pSwitch = document.getElementById('hasPrescriptionSwitch');
            const pForm = document.getElementById('prescriptionForm');
            
            if(pSwitch && pForm) {
                pSwitch.addEventListener('change', (e) => {
                    pForm.style.display = e.target.checked ? 'block' : 'none';
                });
            }

            // Múltiplos Remédios
            const addMedBtn = document.getElementById('addMedBtn');
            const medList = document.getElementById('medList');
            if(addMedBtn && medList) {
                addMedBtn.addEventListener('click', () => {
                    const firstMed = medList.querySelector('.med-item');
                    if(firstMed) {
                         const clone = firstMed.cloneNode(true);
                         clone.querySelector('input').value = '';
                         medList.appendChild(clone);
                    }
                });
                
                medList.addEventListener('click', (e) => {
                    if(e.target.closest('.btn-remove-med')) {
                        const items = medList.querySelectorAll('.med-item');
                        if(items.length > 1) {
                            e.target.closest('.med-item').remove();
                        }
                    }
                });
            }

            // Ativar Tom-Select em multiplos dropdowns
            const tsInstances = {};
            document.querySelectorAll('select.live-search').forEach((el) => {
                tsInstances[el.name] = new TomSelect(el, { create: false, sortField: { field: "text", direction: "asc" }});
            });

            // Binding Auto-Fill da Consulta Ativa
            const appointmentSelect = document.querySelector('select[name="appointment_id"]');
            if (appointmentSelect) {
                appointmentSelect.addEventListener('change', (e) => {
                    const selOpt = e.target.options[e.target.selectedIndex];
                    if(selOpt && selOpt.value) {
                        const cid = selOpt.getAttribute('data-client-id');
                        const did = selOpt.getAttribute('data-doctor-id');
                        if (cid && tsInstances['client_id']) tsInstances['client_id'].setValue(cid);
                        if (did && tsInstances['doctor_id']) tsInstances['doctor_id'].setValue(did);
                    } else {
                        if (tsInstances['client_id']) tsInstances['client_id'].clear();
                        if (tsInstances['doctor_id']) tsInstances['doctor_id'].clear();
                    }
                });
            }

            // Validação de Abas Cruzadas (HTML5 form validation proxy)
            const recordFormNode = document.getElementById('recordForm');
            if (recordFormNode) {
                const inputs = recordFormNode.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('invalid', () => {
                        const pane = input.closest('.tab-pane');
                        if (pane) {
                            const tabId = pane.id;
                            const tabTrigger = document.querySelector(`button[data-bs-target="#${tabId}"]`);
                            if (tabTrigger) {
                                const tabInst = new bootstrap.Tab(tabTrigger);
                                tabInst.show();
                            }
                        }
                        recordFormNode.classList.add('was-validated');
                    }, false);
                });
            }
        });
    </script>
@endpush
