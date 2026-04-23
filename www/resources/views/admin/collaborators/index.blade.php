@extends('layouts.app')

@section('title', 'Gestão de Atendentes - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-person-badge me-2"></i>Gestão de Atendentes (Recepção)</h4>
            <a href="{{ route('collaborators.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Novo Atendente
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-bordered table-striped w-100']) !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
