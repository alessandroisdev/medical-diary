@extends('layouts.app')

@section('title', 'Prontuários e Receitas - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-file-medical me-2"></i>Consultório Base - Medical Records</h4>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#createRecordModal">
                <i class="bi bi-pen me-1"></i> Preencher Prontuário Clínico
            </button>
        </div>

        <div class="card border-0 shadow-sm border-top border-danger border-3">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-hover align-middle w-100']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Modal Prontuário -->
<div class="modal fade" id="createRecordModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('records.store') }}" method="POST" id="recordForm">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="methodInput" disabled>
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-medical me-2"></i>Atendimento Médico - Prontuário Eletrônico</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <!-- Referência Cruzada Opcional -->
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
                        <button class="nav-link fw-bold text-success" data-bs-toggle="pill" data-bs-target="#prescription" type="button" role="tab"><i class="bi bi-capsule me-1"></i>Gerar Receituário</button>
                      </li>
                    </ul>

                    <div class="tab-content border bg-white p-3 rounded" id="recordTabsContent">
                        
                        <!-- TAB EXAME -->
                        <div class="tab-pane fade show active" id="anamnesis" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Sintomatologia e Queixa Principal</label>
                                <textarea name="symptoms" class="form-control" rows="5" placeholder="Relato do paciente..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Anotações Gerais Adicionais</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- TAB DIAGNOSTICO -->
                        <div class="tab-pane fade" id="diagnosis" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Diagnóstico Clínico (CID Opcional)</label>
                                <input type="text" name="diagnosis" class="form-control form-control-lg border-danger" required placeholder="Ex: J03.9 Amigdalite aguda não especificada">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Plano Terapêutico/Conduta</label>
                                <textarea name="treatment_plan" class="form-control" rows="5" required placeholder="Instruções e descrições do tratamento de longo termo..."></textarea>
                            </div>
                        </div>

                        <!-- TAB RECEITA -->
                        <div class="tab-pane fade" id="prescription" role="tabpanel">
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" role="switch" name="has_prescription" id="hasPrescriptionSwitch" value="1">
                                <label class="form-check-label fw-bold" for="hasPrescriptionSwitch">Emitir Assinatura Digital e Receituário Múltiplo para Impressão?</label>
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
                                    <textarea name="instructions" class="form-control" rows="4" placeholder="Tomar 1 comp a cada 8h..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abadonar Preenchimento</button>
                    <!-- Double click native disabled inside app.ts prevents saving twice -->
                    <button type="submit" class="btn btn-danger px-4 fw-bold" data-original-text="Assinar Digitalmente e Salvar Prontuário">Assinar Digitalmente e Salvar Prontuário</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
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
                        // Limpa
                        if (tsInstances['client_id']) tsInstances['client_id'].clear();
                        if (tsInstances['doctor_id']) tsInstances['doctor_id'].clear();
                    }
                });
            }

            // Validação de Abas Cruzadas (HTML5 form validation proxy)
            const recordFormNode = document.getElementById('recordForm');
            if (recordFormNode) {
                // Intercepta quando o botão "submit" força a validação html5 (Invalid Event)
                const inputs = recordFormNode.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('invalid', () => {
                        // Encontra a qual aba ele pertence
                        const pane = input.closest('.tab-pane');
                        if (pane) {
                            const tabId = pane.id;
                            const tabTrigger = document.querySelector(`button[data-bs-target="#${tabId}"]`);
                            if (tabTrigger) {
                                // Força abrir a aba com problema para o usuário ver
                                const tabInst = new bootstrap.Tab(tabTrigger);
                                tabInst.show();
                            }
                        }
                        recordFormNode.classList.add('was-validated');
                    }, false); // capturing false
                });

                // O form vai recarregar o datatable dinamicamente pós XHR AJAX sucesso.
                recordFormNode.addEventListener('submit', (e) => {
                    // Impede recarregar se campos exigidos faltam, já tratado pelo browser, 
                    // contudo, o success hook custom:
                    setTimeout(() => {
                        if(window.LaravelDataTables && window.LaravelDataTables["medical-records-table"]) {
                            window.LaravelDataTables["medical-records-table"].ajax.reload(null, false);
                            const modalNode = document.getElementById('createRecordModal');
                            if(modalNode){
                                const modalInst = bootstrap.Modal.getInstance(modalNode);
                                if(modalInst) modalInst.hide();
                                
                                // Reset tabs visual
                                const firstTab = new bootstrap.Tab(document.querySelector('#recordTabs button[data-bs-target="#anamnesis"]'));
                                if (firstTab) firstTab.show();
                            }
                        }
                    }, 500);
                });
            }
        });

        // Handler Exposto Global para o Editar Prontuário do Datatables
        window.editRecord = function(data) {
            const form = document.getElementById('recordForm');
            // Mudar para Editar
            form.action = `/records/${data.id}`;
            document.getElementById('methodInput').disabled = false;
            
            // Popula os dados cruciais
            const tsInstances = document.querySelector('select[name="client_id"]').tomselect;
             if(tsInstances) tsInstances.setValue(data.client_id);
             
            const docTs = document.querySelector('select[name="doctor_id"]').tomselect;
            if(docTs) docTs.setValue(data.doctor_id);

            if(data.appointment_id) {
                 document.querySelector('select[name="appointment_id"]').value = data.appointment_id;
            }

            document.querySelector('textarea[name="symptoms"]').value = data.symptoms || '';
            document.querySelector('input[name="diagnosis"]').value = data.diagnosis || '';
            document.querySelector('textarea[name="treatment_plan"]').value = data.treatment_plan || '';
            document.querySelector('textarea[name="notes"]').value = data.notes || '';
            
            // Prescription fields
            document.getElementById('hasPrescriptionSwitch').checked = false;
            document.getElementById('prescriptionForm').style.display = 'none';

            // Customiza Botão e Titulo
            document.querySelector('.modal-title').innerHTML = '<i class="bi bi-file-earmark-medical me-2"></i>Edição de Prontuário Clínico';
            const submitBtn = form.querySelector('[type="submit"]');
            submitBtn.innerHTML = 'Assinar e Salvar Ajustes';
            submitBtn.setAttribute('data-original-text', 'Assinar e Salvar Ajustes');

            // Abrir Modal
            const modal = new bootstrap.Modal(document.getElementById('createRecordModal'));
            modal.show();
        };

        // Reseta o Form Action ao fechar para que Novo crie corretamente em seguida
        const modalEl = document.getElementById('createRecordModal');
        if(modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                const form = document.getElementById('recordForm');
                form.action = "{{ route('records.store') }}";
                document.getElementById('methodInput').disabled = true;
                form.reset();
                document.querySelector('.modal-title').innerHTML = '<i class="bi bi-file-earmark-medical me-2"></i>Atendimento Médico - Prontuário Eletrônico';
                const submitBtn = form.querySelector('[type="submit"]');
                submitBtn.innerHTML = 'Assinar Digitalmente e Salvar Prontuário';
                submitBtn.setAttribute('data-original-text', 'Assinar Digitalmente e Salvar Prontuário');
                
                // limpa selects
                const tsClient = document.querySelector('select[name="client_id"]').tomselect;
                if(tsClient) tsClient.clear();
                
                // Redireciona o botao pro original doctor id 
                const docTs = document.querySelector('select[name="doctor_id"]').tomselect;
                // No need to clear, usually logged in doctor is selected, keep it as is or hard reset from session would require blade injection here
            });
        }
    </script>
@endpush
