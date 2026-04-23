@extends('layouts.app')

@section('title', 'Convênios - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-shield-check me-2"></i>Gestão de Convênios / Planos de Saúde</h4>
            <a href="{{ route('health-insurances.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Novo Convênio
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nome do Convênio</th>
                                <th>Código ANS</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($insurances as $item)
                            <tr>
                                <td class="fw-bold">{{ $item->name }}</td>
                                <td>{{ $item->ans_code ?? 'N/A' }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-danger">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('health-insurances.edit', $item->id) }}" class="btn btn-sm btn-outline-info text-dark" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('health-insurances.destroy', $item->id) }}" method="POST" class="m-0 p-0 no-ajax" onsubmit="return confirm('ATENÇÃO: Excluir este convênio destruirá o histórico de preços atrelado a ele. Continuar?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Apagar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Nenhum convênio cadastrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                {{ $insurances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
