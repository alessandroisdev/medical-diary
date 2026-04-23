@extends('layouts.app')

@section('title', 'Novo Médico - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12 col-xl-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-person-plus me-2"></i>Cadastrar Novo Médico</h4>
            <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Tabela
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <form action="{{ route('doctors.store') }}" method="POST" class="no-ajax">
                @csrf
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail (Acesso ao Sistema Clínico)</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-bold">CRM</label>
                            <input type="text" name="crm" class="form-control" required placeholder="Ex: 12345-SP" value="{{ old('crm') }}">
                            @error('crm') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-bold">Duração do Slot (min)</label>
                            <input type="number" name="consultation_duration_minutes" class="form-control" min="10" max="120" step="5" required value="{{ old('consultation_duration_minutes', 30) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-bold">Especialidades Base</label>
                            <select name="specialties[]" class="form-select live-search" multiple required placeholder="Selecione...">
                                @foreach($specialties as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 border-top pt-3 mt-4">
                        <label class="form-label fw-bold text-danger">Senha Inicial Provisória</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <div class="form-text">Mínimo de 6 caracteres. Informar ao médico para alteração posterior.</div>
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer bg-white text-end p-3">
                    <button type="submit" class="btn btn-primary btn-lg px-4 fw-bold">Cadastrar e Ativar Acesso</button>
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
        new TomSelect(el, {
            plugins: ['remove_button'],
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    });
});
</script>
@endpush
@endsection
