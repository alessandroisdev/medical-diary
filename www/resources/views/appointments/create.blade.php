@extends('layouts.app')

@section('title', 'Novo Agendamento / Encaixe')

@section('content')
<div class="row">
    <div class="col-12 col-md-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-calendar-plus me-2"></i>Forçar Encaixe / Agendamento Manual</h4>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Fila
            </a>
        </div>

        <div class="alert alert-warning border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Atenção:</strong> Os agendamentos feitos por esta tela contornam as regras restritivas do motor do paciente. Você tem a liberdade de forçar horários em médicos com a agenda lotada (Overbooking). Use com sabedoria!
        </div>

        <div class="card border-0 shadow-sm border-top border-danger border-3">
            <form action="{{ route('appointments.store') }}" method="POST" class="no-ajax">
                @csrf
                <div class="card-body p-4 bg-light">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Paciente</label>
                            <select class="form-select live-search" name="client_id" required>
                                <option value="" selected disabled>Busque pelo nome ou CPF...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                            @error('client_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Médico Destino</label>
                            <select class="form-select live-search" name="doctor_id" required>
                                <option value="" selected disabled>Procurar médico(a)...</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                            @error('doctor_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Data Alvo</label>
                            <input type="date" name="date" class="form-control" required value="{{ old('date') }}">
                            @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Horário (HH:MM)</label>
                            <input type="time" name="time" class="form-control" required value="{{ old('time') }}">
                            @error('time') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo da Consulta</label>
                            <select name="consultation_type" class="form-select" required>
                                <option value="routine" {{ old('consultation_type') == 'routine' ? 'selected' : '' }}>Rotina / Inicial</option>
                                <option value="return" {{ old('consultation_type') == 'return' ? 'selected' : '' }}>Retorno Gratuito</option>
                                <option value="emergency" {{ old('consultation_type') == 'emergency' ? 'selected' : '' }}>Encaixe Extremo/Urgência</option>
                            </select>
                            @error('consultation_type') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Anotações da Recepção (Forma Pgto / Sintoma inicial / Exame)</label>
                        <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="card-footer bg-white text-end p-3 align-items-center d-flex justify-content-between">
                    <span class="text-muted small"><i class="bi bi-clock-history me-1"></i>A vaga ficará salva imediatamente no calendário do Médico.</span>
                    <button type="submit" class="btn btn-danger px-4 fw-bold shadow"><i class="bi bi-calendar2-plus me-1"></i> Gravar Agendamento Manual</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.live-search').forEach((el) => {
        new TomSelect(el, { create: false, sortField: { field: "text", direction: "asc" } });
    });
});
</script>
@endpush
@endsection
