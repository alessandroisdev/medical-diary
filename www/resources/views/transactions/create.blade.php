@extends('layouts.app')

@section('title', 'Novo Lançamento Financeiro')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-plus-circle me-2"></i>Novo Movimento Contábil</h4>
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Fluxo
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <form action="{{ route('transactions.store') }}" method="POST" class="no-ajax">
                @csrf
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cliente/Paciente (Opcional)</label>
                        <select name="client_id" class="form-select live-search">
                            <option value="">Não Identificado / Lançamento Avulso</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Associar um cliente melhora os relatórios gerenciais depois.</div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Valor (R$)</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required placeholder="0.00" value="{{ old('amount') }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Tipo da Movimentação</label>
                            <select name="type" class="form-select" required>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Receita (Entrada Positiva)</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Despesa (Saída Negativa)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Status de Quitação</label>
                            <select name="status" class="form-select" required>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Já Pago/Quitado</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Aguardando Pagamento/A Vencer</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Data de Vencimento</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Método Base de Faturamento</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="pix" {{ old('payment_method') == 'pix' ? 'selected' : '' }}>Transferência PIX</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="bank_slip" {{ old('payment_method') == 'bank_slip' ? 'selected' : '' }}>Boleto Bancário</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro Físico/Espécie</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Origem/Gateway Contábil</label>
                            <select name="gateway" class="form-select" required>
                                <option value="local">Atendimento Local (Balcão)</option>
                                <option value="asaas">Asaas Pagamentos</option>
                                <option value="pagarme">Pagar.me</option>
                                <option value="mercadopago">Mercado Pago</option>
                                <option value="stripe">Gateway Stripe</option>
                                <option value="paypal">PayPal Global</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end p-3 align-items-center d-flex justify-content-between">
                    <span class="text-muted small">Será registrado em nome de {{ auth()->user()->name }}</span>
                    <button type="submit" class="btn btn-success px-5 fw-bold shadow"><i class="bi bi-check-circle me-1"></i> Finalizar Contabilização</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.live-search').forEach((el) => {
        new TomSelect(el, { create: false, sortField: { field: "text", direction: "asc" } });
    });
});
</script>
@endpush
@endsection
