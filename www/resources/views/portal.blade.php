@extends('layouts.app')

@section('title', 'Meu Portal - Medical Diary')

@section('content')
<div class="row mt-4">
    <div class="col-12 text-center mb-5">
        <h2 class="fw-bold">Área Exclusiva do Paciente</h2>
        <p class="text-muted">Acompanhe seu histórico de agendamentos, receitas prescritas e faturamentos.</p>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-top border-primary border-4 text-center p-4">
            <i class="bi bi-calendar-heart fs-1 text-primary"></i>
            <h4 class="mt-3">Meus Agendamentos</h4>
            <p class="text-muted">Veja as datas das suas próximas consultas e histórico clínico.</p>
            <button class="btn btn-outline-primary" disabled>Visualizar Agenda</button>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-top border-success border-4 text-center p-4">
            <i class="bi bi-file-medical fs-1 text-success"></i>
            <h4 class="mt-3">Receitas e Prontuários</h4>
            <p class="text-muted">Exporte a segunda via das suas prescrições eletrônicas em PDF.</p>
            <button class="btn btn-outline-success" disabled>Acessar Documentos</button>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-top border-warning border-4 text-center p-4">
            <i class="bi bi-wallet2 fs-1 text-warning"></i>
            <h4 class="mt-3">Financeiro</h4>
            <p class="text-muted">Visualize pendências, recibos e realize pagamento via PIX.</p>
            <button class="btn btn-outline-warning" disabled>Central Financeira</button>
        </div>
    </div>
</div>
@endsection
