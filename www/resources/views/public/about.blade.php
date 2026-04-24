@extends('layouts.public')
@section('title', 'Nossa Infraestrutura')

@section('content')
<div class="page-header text-center">
    <div class="container">
        <h1 class="fw-bold">{{ $settings['infra_page_title'] ?? 'Nossa Infraestrutura Hospitalar' }}</h1>
        <p class="fs-5 opacity-75">{{ $settings['infra_page_subtitle'] ?? 'Conheça o padrão de qualidade e tecnologia de nossa matriz.' }}</p>
    </div>
</div>

<section class="section-padding bg-white pt-2">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="{{ $settings['infra_image_url'] ?? 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=800&auto=format&fit=crop' }}" class="img-fluid rounded shadow-lg" alt="Infra Clínica">
            </div>
            <div class="col-lg-6">
                <h3 class="fw-bold mb-4">{{ $settings['infra_title'] ?? 'Mais do que Clínicas, Ecossistemas de Saúde' }}</h3>
                <p class="text-muted fs-5">{{ $settings['infra_text'] ?? 'Acreditamos que o ambiente impacta diretamente a taxa de cura. Por isso as unidades são pensadas para evitar estresse.' }}</p>
                <ul class="list-unstyled mt-4 text-muted">
                    <li class="mb-3"><i class="bi bi-check2-circle text-success me-2 fs-5"></i> {{ $settings['infra_point_1'] ?? 'Isolamento Acústico Premium nos Consultórios' }}</li>
                    <li class="mb-3"><i class="bi bi-check2-circle text-success me-2 fs-5"></i> {{ $settings['infra_point_2'] ?? 'Softwares Autorais de Prontuário para Evitar Filas' }}</li>
                    <li class="mb-3"><i class="bi bi-check2-circle text-success me-2 fs-5"></i> {{ $settings['infra_point_3'] ?? 'Sistema de Exaustão de Ar com Filtro HEPA' }}</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
