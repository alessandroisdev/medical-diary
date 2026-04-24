@extends('layouts.app')

@section('title', 'Configurações Globais (CMS)')

@section('content')
<div class="row">
    <div class="col-12 col-xl-10 mx-auto">
        <h4 class="mb-4 text-dark"><i class="bi bi-gear-fill me-2"></i> Configurações Globais (Mini CMS)</h4>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm border-top border-secondary border-4">
            <form action="{{ route('settings.update') }}" method="POST" class="no-ajax">
                @csrf
                @method('PUT')
                
                <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                    <ul class="nav nav-tabs fw-bold" id="cmsTabs" role="tablist">
                        @php $i = 0; @endphp
                        @foreach($groupedSettings as $groupName => $settingsGroup)
                            @if($settingsGroup->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $i === 0 ? 'active' : '' }}" id="tab-{{ Str::slug($groupName) }}" data-bs-toggle="tab" data-bs-target="#pane-{{ Str::slug($groupName) }}" type="button" role="tab">{{ $groupName }}</button>
                                </li>
                                @php $i++; @endphp
                            @endif
                        @endforeach
                    </ul>
                </div>

                <div class="card-body p-4 bg-light min-vh-50">
                    <div class="tab-content" id="cmsTabsContent">
                        @php $i = 0; @endphp
                        @foreach($groupedSettings as $groupName => $settingsGroup)
                            @if($settingsGroup->count() > 0)
                                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="pane-{{ Str::slug($groupName) }}" role="tabpanel">
                                    
                                    <h5 class="fw-bold mb-4 text-primary border-bottom pb-2">{{ $groupName }}</h5>
                                    
                                    @foreach($settingsGroup as $setting)
                                        <div class="mb-4">
                                            <label class="form-label fw-bold text-dark">{{ preg_replace('/\[.*?\]/', '', $setting->label) }}</label>
                                            
                                            @if($setting->key === 'cancellation_tolerance_hours')
                                                <div class="input-group">
                                                    <input type="number" min="0" step="1" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}" required>
                                                    <span class="input-group-text">Horas de Antecedência</span>
                                                </div>
                                                <div class="form-text text-muted">Aviso prévio mínimo do app para permitir cancelamento de agendamento de paciente.</div>
                                            @elseif(Str::contains($setting->key, 'text') || Str::contains($setting->key, 'message'))
                                                <textarea name="{{ $setting->key }}" class="form-control" rows="4">{{ $setting->value }}</textarea>
                                            @else
                                                <input type="text" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}" required>
                                            @endif
                                        </div>
                                    @endforeach

                                </div>
                                @php $i++; @endphp
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="card-footer bg-white text-end p-3 align-items-center">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow"><i class="bi bi-save me-1"></i> Gravar Alterações no Cache Público</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
