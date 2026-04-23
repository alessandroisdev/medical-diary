<div class="btn-group" role="group">
    <a href="{{ route('transactions.edit', $id) }}" class="btn btn-sm btn-outline-info text-dark" title="Editar Lançamento">
        <i class="bi bi-pencil-square"></i> Editar
    </a>
    <form action="{{ route('transactions.destroy', $id) }}" method="POST" class="m-0 p-0 no-ajax" onsubmit="return confirm('Deseja estornar (SoftDelete) este lançamento?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Estornar"><i class="bi bi-archive"></i> Estornar</button>
    </form>
</div>
