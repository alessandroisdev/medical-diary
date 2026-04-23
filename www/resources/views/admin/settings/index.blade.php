@extends('layouts.app')

@section('title', 'Configurações Globais do Sistema')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <h4 class="mb-4 text-dark"><i class="bi bi-gear-fill me-2"></i> Configurações Globais</h4>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm border-top border-secondary border-4">
            <form action="{{ route('settings.update') }}" method="POST" class="no-ajax">
                @csrf
                @method('PUT')
                <div class="card-body p-4 bg-light">
                    
                    <h5 class="fw-bold mb-3 text-primary border-bottom pb-2">Regras de Negócio - Logística do Paciente</h5>
                    
                    @foreach($settings as $setting)
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">{{ $setting->label }}</label>
                            @if($setting->key === 'cancellation_tolerance_hours')
                                <div class="input-group">
                                    <input type="number" min="0" step="1" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}" required>
                                    <span class="input-group-text">Horas de Antecedência</span>
                                </div>
                                <div class="form-text text-muted">
                                    Define quantas horas <strong>ANTES</strong> da consulta o Paciente é proibido de cancelar através do próprio aplicativo Web. Se colocar "0", ele pode cancelar em qualquer instante.
                                </div>
                            @else
                                <input type="text" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}" required>
                            @endif
                        </div>
                    @endforeach

                </div>
                <div class="card-footer bg-white text-end p-3 align-items-center">
                    <button type="submit" class="btn btn-secondary px-5 fw-bold shadow"><i class="bi bi-save me-1"></i> Gravar Parâmetros Globais</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
