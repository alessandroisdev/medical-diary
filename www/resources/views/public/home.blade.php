@extends('layouts.public')
@section('title', 'Início')

@section('content')
<!-- Hero Section -->
<section class="hero text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1>Sua Saúde Mapeada Digitalmente.</h1>
                <p class="mt-4 mb-5">
                    Consultas simplificadas, prontuário seguro em Nuvem e comunicação direta. Bem-vindo à evolução da saúde conectada para pacientes e médicos experientes.
                </p>
                <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-bold rounded-pill text-primary px-5 py-3 shadow">Agendar via Portal</a>
            </div>
            <div class="col-lg-6 text-center">
                <div style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                    <i class="bi bi-shield-lock text-success" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Acesso 100% Protegido</h3>
                    <p class="opacity-75">Suas informações clínicas contam com criptografia de ponta a ponta e rígido isolamento em servidores blindados.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Especialidades Destaque -->
<section class="section-padding bg-light-subtle">
    <div class="container text-center">
        <span class="text-primary fw-bold text-uppercase tracking-wider small">Bem-vindo</span>
        <h2 class="fw-bold mt-2 mb-4">Referência em Cuidado Contínuo</h2>
        <p class="text-muted w-75 mx-auto mb-5">Somos focados em prover aos nossos clientes uma plataforma inteligente e segura. Escolha um de nossos canais acima para conhecer a clínica ou acesse o Portal para Agendar seus horários.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('public.doctors') }}" class="btn btn-outline-primary fw-bold px-4"><i class="bi bi-search me-1"></i> Procurar Especialistas</a>
            <a href="{{ route('public.contact') }}" class="btn btn-primary fw-bold px-4"><i class="bi bi-telephone me-1"></i> Fale Conosco</a>
        </div>
    </div>
</section>
@endsection
