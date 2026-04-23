@extends('layouts.app')

@section('title', 'Editar Movimentação Contábil')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-pencil-square me-2"></i>Editar Lançamento Contábil #{{ $transaction->id }}</h4>
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Fluxo
            </a>
        </div>

        <div class="card border-0 shadow-sm border-top border-warning border-3">
            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="no-ajax">
                @csrf
                @method('PUT')
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cliente/Paciente Vinculado</label>
                        <select name="client_id" class="form-select live-search">
                            <option value="">Não Identificado / Lançamento Avulso</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $transaction->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Valor Transacionado (R$)</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required value="{{ old('amount', $transaction->amount) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Receita (Entrada Positiva)</option>
                                <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Despesa (Saída Negativa)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Status de Quitação</label>
                            <select name="status" class="form-select" required>
                                <option value="paid" {{ old('status', $transaction->status) == 'paid' ? 'selected' : '' }}>Já Pago/Quitado</option>
                                <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Aguardando Pagamento/A Vencer</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Vencimento Original</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $transaction->due_date ? clone $transaction->due_date : '') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Método</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="pix" {{ old('payment_method', $transaction->payment_method) == 'pix' ? 'selected' : '' }}>Transferência PIX</option>
                                <option value="credit_card" {{ old('payment_method', $transaction->payment_method) == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="cash" {{ old('payment_method', $transaction->payment_method) == 'cash' ? 'selected' : '' }}>Dinheiro Físico/Espécie</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Gateway Contábil</label>
                            <select name="gateway" class="form-select" required>
                                <option value="local" {{ old('gateway', $transaction->gateway) == 'local' ? 'selected' : '' }}>Atendimento Local (Balcão)</option>
                                <option value="asaas" {{ old('gateway', $transaction->gateway) == 'asaas' ? 'selected' : '' }}>Asaas Pagamentos</option>
                                <option value="pagarme" {{ old('gateway', $transaction->gateway) == 'pagarme' ? 'selected' : '' }}>Pagar.me</option>
                                <option value="stripe" {{ old('gateway', $transaction->gateway) == 'stripe' ? 'selected' : '' }}>Gateway Stripe</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-white text-end p-3 align-items-center d-flex justify-content-between">
                    <span class="text-danger small fw-bold">A edição altera relatórios financeiros históricos globalmente.</span>
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow text-dark"><i class="bi bi-save me-1"></i> Gravar Alterações</button>
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
