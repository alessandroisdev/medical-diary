@extends('layouts.public')
@section('title', 'Médicos e Equipe')

@section('content')
<div class="page-header text-center">
    <div class="container">
        <h1 class="fw-bold">Nosso Corpo Clínico</h1>
        <p class="fs-5 opacity-75">Profissionais selecionados focados no cuidado humanizado e preciso.</p>
    </div>
</div>

<section class="section-padding bg-white pt-2">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @forelse($doctors as $doc)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm text-center p-4 h-100">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($doc->name) }}&background=e0e7ff&color=4f46e5&size=128" class="avatar-doc mx-auto mb-3" alt="{{ $doc->name }}">
                        <h5 class="fw-bold text-dark">{{ $doc->name }}</h5>
                        <span class="badge bg-light text-secondary border mb-2"><i class="bi bi-card-heading me-1"></i> {{ $doc->crm }}</span>
                        <div class="small fw-bold text-primary">
                            {{ $doc->specialties->pluck('name')->implode(', ') ?: 'Clínica Geral' }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center text-muted">
                    <i class="bi bi-person-x fs-1 mb-3 d-block"></i>
                    Buscando integração com dados do RH Médico.
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
