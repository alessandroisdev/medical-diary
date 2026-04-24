@extends('layouts.public')
@section('title', 'Especialidades Oferecidas')

@section('content')
<div class="page-header text-center">
    <div class="container">
        <h1 class="fw-bold">Nossas Especialidades</h1>
        <p class="fs-5 opacity-75">Soluções direcionadas para promover seu bem-estar completo.</p>
    </div>
</div>

<section class="section-padding bg-light-subtle pt-2">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @forelse($specialties as $sp)
                <div class="col-md-4 col-lg-3">
                    <div class="card-specialty bg-white p-4 text-center h-100">
                        <i class="bi bi-diagram-3 text-warning fs-1 mb-3"></i>
                        <h5 class="fw-bold">{{ $sp->name }}</h5>
                        <p class="text-muted small mb-0">{{ $sp->description ?: 'Atendimentos focados em alta complexidade com acompanhamento humano.' }}</p>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center text-muted">
                    <i class="bi bi-x-circle fs-1 mb-3 d-block"></i>
                    A estrutura clínica está passando por mapeamento de dados. Volte em breve.
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
