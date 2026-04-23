@extends('layouts.app')

@section('title', 'Novo Convênio')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-plus-circle me-2"></i>Cadastrar Convênio</h4>
            <a href="{{ route('health-insurances.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <form action="{{ route('health-insurances.store') }}" method="POST" class="no-ajax">
                @csrf
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome da Operadora/Plano</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="Ex: Unimed Nacional">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Código ANS (Opcional)</label>
                        <input type="text" name="ans_code" class="form-control" value="{{ old('ans_code') }}">
                        @error('ans_code') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3 form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                        <label class="form-check-label fw-bold" for="isActive">Habilitado para precificação e uso no sistema</label>
                    </div>
                </div>
                <div class="card-footer bg-white text-end p-3">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Salvar Convênio</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
