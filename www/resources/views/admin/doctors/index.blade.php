@extends('layouts.app')

@section('title', 'Gestão de Médicos - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-person-heart me-2"></i>Corpo Clínico (Médicos)</h4>
            <a href="{{ route('doctors.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Novo Médico
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
