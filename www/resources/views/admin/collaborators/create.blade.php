@extends('layouts.app')

@section('title', 'Novo Atendente - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12 col-xl-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-person-plus me-2"></i>Cadastrar Novo Atendente (Recepção)</h4>
            <a href="{{ route('collaborators.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Tabela
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <form action="{{ route('collaborators.store') }}" method="POST" class="no-ajax">
                @csrf
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail (Login de Acesso)</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3 border-top pt-3 mt-4">
                        <label class="form-label fw-bold text-danger">Senha Inicial Provisória</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <div class="form-text">Mínimo de 6 caracteres. Importante para o acesso aos painéis de chamada de ficha.</div>
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
@endsection
