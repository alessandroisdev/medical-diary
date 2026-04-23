@extends('layouts.app')

@section('title', 'Prontuários e Receitas - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-file-medical me-2"></i>Consultório Base - Medical Records</h4>
            <a href="{{ route('records.create') }}" class="btn btn-danger">
                <i class="bi bi-pen me-1"></i> Preencher Prontuário Clínico
            </a>
        </div>

        <div class="card border-0 shadow-sm border-top border-danger border-3">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-hover align-middle w-100']) !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
