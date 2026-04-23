@extends('layouts.app')

@section('title', 'Especialidades - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-tags me-2"></i>Especialidades Clínicas Reais</h4>
            <a href="{{ route('specialties.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nova Especialidade
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nome da Especialidade</th>
                                <th>Descrição (Opcional)</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($specialties as $item)
                            <tr>
                                <td class="fw-bold">{{ $item->name }}</td>
                                <td>{{ Str::limit($item->description, 50) }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('specialties.edit', $item->id) }}" class="btn btn-sm btn-outline-info text-dark" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('specialties.destroy', $item->id) }}" method="POST" class="m-0 p-0 no-ajax" onsubmit="return confirm('Excluir esta especialidade?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Apagar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Nenhuma especialidade cadastrada.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                {{ $specialties->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
