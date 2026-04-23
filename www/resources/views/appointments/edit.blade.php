@extends('layouts.app')

@section('title', 'Editar Ficha da Recepção')

@section('content')
<div class="row">
    <div class="col-12 col-md-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-pencil-square me-2"></i>Edição de Ficha Logística</h4>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Fila
            </a>
        </div>

        <div class="card border-0 shadow-sm border-top border-info border-3">
            <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" class="no-ajax">
                @csrf
                @method('PUT')
                <div class="card-body p-4 bg-light">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Paciente Vinculado</label>
                            <input type="text" class="form-control" disabled value="{{ $appointment->client->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Médico</label>
                            <input type="text" class="form-control" disabled value="{{ $appointment->doctor->name }}">
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Data Registrada</label>
                            <input type="date" name="date" class="form-control" required value="{{ old('date', \Carbon\Carbon::parse($appointment->scheduled_at)->format('Y-m-d')) }}">
                            @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Horário (HH:MM)</label>
                            <input type="time" name="time" class="form-control" required value="{{ old('time', \Carbon\Carbon::parse($appointment->scheduled_at)->format('H:i')) }}">
                            @error('time') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo da Consulta</label>
                            <select name="consultation_type" class="form-select" required>
                                <option value="routine" {{ old('consultation_type', $appointment->consultation_type) == 'routine' ? 'selected' : '' }}>Rotina / Inicial</option>
                                <option value="return" {{ old('consultation_type', $appointment->consultation_type) == 'return' ? 'selected' : '' }}>Retorno Gratuito</option>
                                <option value="emergency" {{ old('consultation_type', $appointment->consultation_type) == 'emergency' ? 'selected' : '' }}>Encaixe Extremo/Urgência</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label fw-bold text-primary">Status Sistêmico</label>
                            <select name="status" class="form-select fw-bold" required>
                                <option value="scheduled" {{ $appointment->status == 'scheduled' ? 'selected' : '' }}>Agendado pelo Paciente</option>
                                <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmado via WhatsApp/App</option>
                                <option value="arrived" {{ $appointment->status == 'arrived' ? 'selected' : '' }}>Fez o Check-In no Saguão</option>
                                <option value="in_consultation" {{ $appointment->status == 'in_consultation' ? 'selected' : '' }}>Em Consulta no Consultório</option>
                                <option value="finished" {{ $appointment->status == 'finished' ? 'selected' : '' }}>Finalizado com Sucesso e Pago</option>
                                <option value="no_show" {{ $appointment->status == 'no_show' ? 'selected' : '' }}>Paciente Faltou sem Aviso</option>
                                <option value="canceled" {{ $appointment->status == 'canceled' ? 'selected' : '' }}>Cancelado Pelo Médico/Recepção</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-4">
                        <label class="form-label fw-bold">Anotações da Ficha da Recepção</label>
                        <textarea class="form-control" name="notes" rows="4">{{ old('notes', $appointment->notes) }}</textarea>
                    </div>
                </div>
                <div class="card-footer bg-white text-end p-3">
                    <button type="submit" class="btn btn-info text-dark px-4 fw-bold shadow"><i class="bi bi-save me-1"></i> Confirmar Alterações Logísticas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
