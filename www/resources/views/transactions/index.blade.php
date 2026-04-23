@extends('layouts.app')

@section('title', 'Financeiro - Medical Diary')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success border-0 shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-semibold"><i class="bi bi-wallet2 me-2"></i>Receita (Mês)</h6>
                <h3 class="fw-bold mb-0">R$ {{ number_format($totalIncome, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-dark bg-warning border-0 shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-semibold"><i class="bi bi-hourglass-split me-2"></i>A Receber</h6>
                <h3 class="fw-bold mb-0">R$ {{ number_format($totalPending, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Novos Gráficos -->
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-muted mb-3">Faturamento Mensal (Atual)</h6>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-muted mb-3">Meios de Recebimento</h6>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="gatewayChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-cash-stack me-2"></i>Lançamentos Financeiros</h4>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary shadow-sm fw-bold">
                <i class="bi bi-plus-circle me-1"></i> Adicionar Manualmente
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-bordered table-striped w-100']) !!}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // Engine Chart.js
            fetch('{{ route('transactions.metrics') }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
                    new Chart(ctxMonthly, {
                        type: 'bar',
                        data: {
                            labels: data.monthly.labels,
                            datasets: [{
                                label: 'Receita Bruta (R$)',
                                data: data.monthly.data,
                                backgroundColor: 'rgba(52, 211, 153, 0.7)',
                                borderColor: '#10b981',
                                borderWidth: 1,
                                borderRadius: 5
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });

                    const ctxGateway = document.getElementById('gatewayChart').getContext('2d');
                    new Chart(ctxGateway, {
                        type: 'doughnut',
                        data: {
                            labels: data.gateways.labels,
                            datasets: [{
                                data: data.gateways.data,
                                backgroundColor: ['#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444', '#10b981'],
                                borderWidth: 0
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                })
                .catch(e => console.error('Erro ao montar metadados ChartJS', e));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
