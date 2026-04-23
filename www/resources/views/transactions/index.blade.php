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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
                <i class="bi bi-plus-circle me-1"></i> Novo Lançamento
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-bordered table-striped w-100']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Novo Lançamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente/Paciente (Opcional)</label>
                        <select name="client_id" class="form-select">
                            <option value="">Não Identificado</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Valor (R$)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="income">Receita (Entrada)</option>
                                <option value="expense">Despesa (Saída)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="paid">Pago</option>
                                <option value="pending">Pendente</option>
                            </select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Vencimento</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Método / Gateway</label>
                        <div class="input-group">
                            <select name="payment_method" class="form-select" required>
                                <option value="pix">PIX</option>
                                <option value="credit_card">Cartão de Crédito</option>
                                <option value="cash">Dinheiro em Espécie</option>
                            </select>
                            <select name="gateway" class="form-select" required>
                                <option value="local">Presencial (Local)</option>
                                <option value="asaas">Asaas</option>
                                <option value="pagarme">Pagar.me</option>
                                <option value="mercadopago">Mercado Pago</option>
                                <option value="stripe">Stripe</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" data-original-text="Salvar">Salvar Movimentação</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             const forms = document.querySelectorAll('form');
             forms.forEach(f => {
                f.addEventListener('submit', () => {
                    setTimeout(() => {
                        if(window.LaravelDataTables && window.LaravelDataTables["transactions-table"]) {
                            window.LaravelDataTables["transactions-table"].ajax.reload(null, false);
                            const modalNode = document.getElementById('createTransactionModal');
                            if(modalNode){
                                const modalInst = bootstrap.Modal.getInstance(modalNode);
                                if(modalInst) modalInst.hide();
                            }
                        }
                    }, 500);
                });
             });

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
