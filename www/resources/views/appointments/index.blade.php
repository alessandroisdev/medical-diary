@extends('layouts.app')

@section('title', 'Agendamentos - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-calendar-check me-2"></i>Agendamentos</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                <i class="bi bi-plus-circle me-1"></i> Novo Agendamento
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-bordered table-striped w-100']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Novo Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Médico</label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data e Hora</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Consulta</label>
                        <select name="consultation_type" class="form-select" required>
                            <option value="routine">Rotina</option>
                            <option value="first_time">Primeira Vez</option>
                            <option value="return">Retorno</option>
                            <option value="surgery">Cirurgia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anotações Internas</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" data-original-text="Salvar Agendamento">Salvar Agendamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        // Quando o Ajax termina (nosso interceptor app.ts processa e limpa form), 
        // vamos atualizar o Datatable se houver sucesso.
        document.addEventListener('DOMContentLoaded', () => {
             // Como usamos o jQuery DataTables script no layout, podemos recarregar usando o ID.
             const forms = document.querySelectorAll('form');
             forms.forEach(f => {
                f.addEventListener('submit', () => {
                    setTimeout(() => {
                        if(window.LaravelDataTables && window.LaravelDataTables["appointments-table"]) {
                            window.LaravelDataTables["appointments-table"].ajax.reload(null, false);
                            
                            // Tenta fechar o modal
                            const modalNode = document.getElementById('createAppointmentModal');
                            if(modalNode){
                                const modalInst = bootstrap.Modal.getInstance(modalNode);
                                if(modalInst) modalInst.hide();
                            }
                        }
                    }, 500); // tempo de espera leve até o fetch resolver no ts (exemplo genérico)
                });
             });
        });
    </script>
@endpush
