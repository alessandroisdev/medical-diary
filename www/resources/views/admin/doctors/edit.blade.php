@extends('layouts.app')

@section('title', 'Editar Médico - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12 col-xl-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-pencil-square me-2"></i>Edição de Acesso Clínico</h4>
            <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Tabela
            </a>
        </div>

        <div class="card border-0 shadow-sm border-top border-warning border-3">
            <form action="{{ route('doctors.update', $doctor->id) }}" method="POST" class="no-ajax">
                @csrf
                @method('PUT')
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $doctor->name) }}">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail (Acesso ao Sistema Clínico)</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email', $doctor->email) }}">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">CRM</label>
                            <input type="text" name="crm" class="form-control" required placeholder="Ex: 12345-SP" value="{{ old('crm', $doctor->crm) }}">
                            @error('crm') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Especialidade (Opcional)</label>
                            <input type="text" name="specialty" class="form-control" placeholder="Pediatria, Clínica Geral..." value="{{ old('specialty', $doctor->specialty) }}">
                        </div>
                    </div>
                    <div class="mb-3 border-top pt-3 mt-4">
                        <label class="form-label text-secondary fw-bold"><i class="bi bi-key me-1"></i>Redefinir Senha de Acesso (Opcional)</label>
                        <input type="password" name="password" class="form-control" minlength="6" placeholder="Deixe em branco para não alterar">
                        <div class="form-text">Preencha apenas se precisar forçar uma nova senha para este médico.</div>
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer bg-white text-end p-3">
                    <button type="submit" class="btn btn-warning btn-lg px-4 fw-bold text-dark">Atualizar Credenciais Cadastrais</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
