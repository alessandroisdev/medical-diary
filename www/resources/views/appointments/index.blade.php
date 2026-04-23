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

        <div class="alert alert-primary shadow-sm border-0 d-flex align-items-center">
            <i class="bi bi-info-circle fa-2x me-3"></i>
            <div>
                Aqui você visualiza toda a fila da clínica. Utilize a coluna <b>Ações</b> para fazer o Check-In do paciente quando ele chegar, e depois utilize o botão <b>Chamar na TV do Saguão</b> para notificar que é a vez dele.
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
    {!! $dataTable->scripts() !!}
@endpush
