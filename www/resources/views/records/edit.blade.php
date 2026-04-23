@extends('layouts.app')

@section('title', 'Editar Evolução Clínica - Medical Diary')

@section('content')
<div class="row mb-5">
    <div class="col-12 col-xl-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-pencil-square me-2"></i>Edição de Prontuário Clínico (Evolução)</h4>
            <a href="{{ route('records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Lista
            </a>
        </div>

        <div class="card border-0 shadow-lg border-top border-warning border-3">
            <form action="{{ route('records.update', $record->id) }}" method="POST" id="recordForm">
                @csrf
                @method('PUT')
                <div class="card-body bg-light p-4">
                    <!-- Referência Cruzada -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Consulta Ativa (Origem)</label>
                            <select name="appointment_id" class="form-select border-warning">
                                <option value="">Atendimento Avulso (Sem Agendamento prévio)</option>
                                @foreach($appointments as $app)
                                    <option value="{{ $app->id }}" @selected($record->appointment_id == $app->id)>Sala de Espera: {{ $app->client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Paciente Vinculado</label>
                            <select name="client_id" class="form-select live-search" required>
                                <option value="">Busca Rápida...</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" @selected($record->client_id == $c->id)>{{ $c->name }} (CPF: {{ $c->cpf ?? 'S/N' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Médico Registrado na Época</label>
                            <select name="doctor_id" class="form-select live-search" required>
                                <option value="">Assinatura...</option>
                                @foreach($doctors as $d)
                                    <option value="{{ $d->id }}" @selected($record->doctor_id == $d->id)>Dr(a). {{ $d->name }} (CRM: {{ $d->crm }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Abas internas -->
                    <ul class="nav nav-pills mb-3" id="recordTabs" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#anamnesis" type="button" role="tab"><i class="bi bi-clipboard-pulse me-1"></i>Evolução de Sintomas</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-danger border-danger" data-bs-toggle="pill" data-bs-target="#diagnosis" type="button" role="tab"><i class="bi bi-heart-pulse me-1"></i>Ajuste Diagnóstico</button>
                      </li>
                    </ul>

                    <div class="tab-content border bg-white p-4 rounded shadow-sm" id="recordTabsContent">
                        
                        <!-- TAB EXAME -->
                        <div class="tab-pane fade show active" id="anamnesis" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-primary">Sintomatologia e Queixa Principal</label>
                                <textarea name="symptoms" class="form-control" rows="8" placeholder="Relato...">{{ $record->symptoms }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Anotações Gerais Adicionais</label>
                                <textarea name="notes" class="form-control" rows="4">{{ $record->notes }}</textarea>
                            </div>
                        </div>

                        <!-- TAB DIAGNOSTICO -->
                        <div class="tab-pane fade" id="diagnosis" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-danger">Diagnóstico Clínico Específico (CID Opcional)</label>
                                <input type="text" name="diagnosis" class="form-control form-control-lg border-danger" required value="{{ $record->diagnosis }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Plano Terapêutico/Conduta Adotada</label>
                                <textarea name="treatment_plan" class="form-control" rows="8" required>{{ $record->treatment_plan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="bi bi-shield-lock me-1"></i>O receituário original é auditado no banco de dados e não pode ser reescrito aqui.</span>
                    <button type="submit" class="btn btn-warning btn-lg px-5 shadow-sm fw-bold text-dark" data-original-text="Salvar Nova Assinatura de Evolução">Salvar Nova Assinatura de Evolução</button>
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
            // Ativar Tom-Select
            const tsInstances = {};
            document.querySelectorAll('select.live-search').forEach((el) => {
                tsInstances[el.name] = new TomSelect(el, { create: false, sortField: { field: "text", direction: "asc" }});
            });

            // Validação Cruzada (Cross-Tab)
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
