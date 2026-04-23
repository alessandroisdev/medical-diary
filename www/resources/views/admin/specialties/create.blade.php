@extends('layouts.app')

@section('title', 'Nova Especialidade')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-plus-circle me-2"></i>Cadastrar Especialidade</h4>
            <a href="{{ route('specialties.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <form action="{{ route('specialties.store') }}" method="POST" class="no-ajax">
                @csrf
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome da Especialidade</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="Ex: Cardiologia">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição (Opcional)</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="card-footer bg-white text-end p-3">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Salvar Especialidade</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
