@extends('layouts.app')

@section('title', 'Editar Médico - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12 col-xl-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-pencil-square me-2"></i>Edição Clínico-Financeira do Médico</h4>
            <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Tabela
            </a>
        </div>

        <form action="{{ route('doctors.update', $doctor->id) }}" method="POST" class="no-ajax" id="frmDoctor">
            @csrf
            @method('PUT')
            
            <div class="card border-0 shadow-sm border-top border-warning border-3 mb-4">
                <div class="card-header bg-white pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="doctorTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-dark" id="cadastro-tab" data-bs-toggle="tab" data-bs-target="#cadastro" type="button" role="tab"><i class="bi bi-person-badge me-1"></i> Cadastro & Especialidades</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-success" id="financas-tab" data-bs-toggle="tab" data-bs-target="#financas" type="button" role="tab"><i class="bi bi-currency-dollar me-1"></i> Precificação Base</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-primary" id="agenda-tab" data-bs-toggle="tab" data-bs-target="#agenda" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Escala de Disponibilidade</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="doctorTabsContent">
                        
                        <!-- TAB CADASTRO -->
                        <div class="tab-pane fade show active p-4 bg-light" id="cadastro" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nome Completo</label>
                                    <input type="text" name="name" class="form-control" required value="{{ old('name', $doctor->name) }}">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">E-mail (Login)</label>
                                    <input type="email" name="email" class="form-control" required value="{{ old('email', $doctor->email) }}">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">CRM</label>
                                    <input type="text" name="crm" class="form-control" required value="{{ old('crm', $doctor->crm) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Duração do Slot Ocioso (min)</label>
                                    <input type="number" name="consultation_duration_minutes" class="form-control" min="10" max="120" step="5" required value="{{ old('consultation_duration_minutes', $doctor->consultation_duration_minutes) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-secondary fw-bold"><i class="bi bi-key me-1"></i>Nova Senha (Opcional)</label>
                                    <input type="password" name="password" class="form-control" minlength="6" placeholder="Manter atual...">
                                </div>
                                <div class="col-12 mb-3 mt-4">
                                    <label class="form-label fw-bold text-primary"><i class="bi bi-tags me-1"></i> Especialidades de Atendimento Diário</label>
                                    <select name="specialties[]" class="form-select live-search" multiple required placeholder="Vincule uma ou mais especialidades...">
                                        @foreach($specialties as $sp)
                                            <option value="{{ $sp->id }}" {{ $doctor->specialties->contains($sp->id) ? 'selected' : '' }}>{{ $sp->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">As especialidades selecionadas liberarão os horários no portal do cliente.</div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB FINANÇAS -->
                        <div class="tab-pane fade p-4 bg-light" id="financas" role="tabpanel">
                            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                                <i class="bi bi-info-circle-fill fa-2x me-3"></i>
                                <div>
                                    Deixe o campo em branco (vazio) caso o médico não atenda por aquele plano em específico. Valores fixados em R$ 0,00 indicam gratuidade/retorno.
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-12 col-md-6 border-end">
                                    <h5 class="text-secondary fw-bold mb-3 border-bottom pb-2">Atendimento Avulso (Particular)</h5>
                                    @php
                                        $particularPrice = $doctor->prices->where('health_insurance_id', null)->first();
                                    @endphp
                                    <div class="input-group mb-3 shadow-sm rounded">
                                        <span class="input-group-text bg-white fw-bold">R$</span>
                                        <input type="number" name="price_particular" class="form-control border-start-0 ps-0" step="0.01" min="0" value="{{ old('price_particular', $particularPrice ? $particularPrice->price : '') }}" placeholder="Qual o valor da consulta particular?">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <h5 class="text-secondary fw-bold mb-3 border-bottom pb-2">Atendimento por Convênios Ativos</h5>
                                    @forelse($insurances as $plan)
                                        @php
                                            $planPrice = $doctor->prices->where('health_insurance_id', $plan->id)->first();
                                        @endphp
                                        <div class="mb-3 d-flex align-items-center gap-3">
                                            <div class="fw-bold text-dark w-50"><i class="bi bi-shield-check text-success me-1"></i> {{ $plan->name }}</div>
                                            <div class="input-group input-group-sm w-50">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" name="prices[{{ $plan->id }}]" class="form-control" step="0.01" min="0" value="{{ old('prices.'.$plan->id, $planPrice ? $planPrice->price : '') }}" placeholder="Repasse">
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted small">Nenhum convênio cadastrado no sistema.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- TAB AGENDA / DISPONIBILIDADE -->
                        <div class="tab-pane fade p-4 bg-light" id="agenda" role="tabpanel">
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                                <i class="bi bi-calendar-week-fill fa-2x me-3"></i>
                                <div class="small">
                                    Defina a <strong>Escala Semanal</strong>. Informe o Dia, a Especialidade (ex: Clínica Geral nas Manhãs, Pediatria nas Tardes) e a faixa de Horário. Os turnos sem nenhum registro bloqueiam a busca do paciente no portal.
                                </div>
                            </div>

                            <div class="table-responsive bg-white rounded border">
                                <table class="table table-bordered mb-0" id="tbAvailabilities">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Dia da Semana</th>
                                            <th>Especialidade a Atender</th>
                                            <th style="width:150px">Início</th>
                                            <th style="width:150px">Fim</th>
                                            <th style="width:60px" class="text-center">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($doctor->availabilities as $idx => $av)
                                        <tr>
                                            <td>
                                                <select name="availabilities[{{$idx}}][day]" class="form-select form-select-sm" required>
                                                    <option value="0" {{ $av->day_of_week == 0 ? 'selected' : '' }}>Domingo</option>
                                                    <option value="1" {{ $av->day_of_week == 1 ? 'selected' : '' }}>Segunda-feira</option>
                                                    <option value="2" {{ $av->day_of_week == 2 ? 'selected' : '' }}>Terça-feira</option>
                                                    <option value="3" {{ $av->day_of_week == 3 ? 'selected' : '' }}>Quarta-feira</option>
                                                    <option value="4" {{ $av->day_of_week == 4 ? 'selected' : '' }}>Quinta-feira</option>
                                                    <option value="5" {{ $av->day_of_week == 5 ? 'selected' : '' }}>Sexta-feira</option>
                                                    <option value="6" {{ $av->day_of_week == 6 ? 'selected' : '' }}>Sábado</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="availabilities[{{$idx}}][specialty_id]" class="form-select form-select-sm" required>
                                                    @foreach($specialties as $sp)
                                                        <option value="{{ $sp->id }}" {{ $av->specialty_id == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" name="availabilities[{{$idx}}][start_time]" class="form-control form-control-sm" required value="{{ \Carbon\Carbon::parse($av->start_time)->format('H:i') }}"></td>
                                            <td><input type="time" name="availabilities[{{$idx}}][end_time]" class="form-control form-control-sm" required value="{{ \Carbon\Carbon::parse($av->end_time)->format('H:i') }}"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="bg-light text-center">
                                                <button type="button" class="btn btn-sm btn-primary" id="btnAddRow"><i class="bi bi-plus-circle me-1"></i>Adicionar Novo Turno na Semana</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer bg-white text-end p-4">
                    <!-- O submit submeterá os dados de todas as abas juntas -->
                    <button type="submit" class="btn btn-success btn-lg px-5 fw-bold shadow">Salvar Todo o Painel de Configuração e Matriz do Médico</button>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="tplActivityRow">
    <tr>
        <td>
            <select name="availabilities[__IDX__][day]" class="form-select form-select-sm" required>
                <option value="" disabled selected>Selecione...</option>
                <option value="0">Domingo</option>
                <option value="1">Segunda-feira</option>
                <option value="2">Terça-feira</option>
                <option value="3">Quarta-feira</option>
                <option value="4">Quinta-feira</option>
                <option value="5">Sexta-feira</option>
                <option value="6">Sábado</option>
            </select>
        </td>
        <td>
            <select name="availabilities[__IDX__][specialty_id]" class="form-select form-select-sm" required>
                <option value="" disabled selected>Selecione...</option>
                @foreach($specialties as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="time" name="availabilities[__IDX__][start_time]" class="form-control form-control-sm" required></td>
        <td><input type="time" name="availabilities[__IDX__][end_time]" class="form-control form-control-sm" required></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // TomSelect para as caixas live-search
    document.querySelectorAll('.live-search').forEach((el) => {
        new TomSelect(el, {
            plugins: ['remove_button'],
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    });

    let avIndex = {{ count($doctor->availabilities) }};
    const tpl = document.getElementById('tplActivityRow').innerHTML;
    const tBody = document.querySelector('#tbAvailabilities tbody');

    document.getElementById('btnAddRow').addEventListener('click', function() {
        const tr = document.createElement('tbody');
        tr.innerHTML = tpl.replace(/__IDX__/g, avIndex++);
        tBody.appendChild(tr.firstElementChild);
    });

    tBody.addEventListener('click', function(e) {
        if(e.target.closest('.btn-remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endpush
@endsection
