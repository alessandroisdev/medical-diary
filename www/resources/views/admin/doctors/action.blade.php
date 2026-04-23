<div class="btn-group" role="group">
    <a href="{{ route('doctors.edit', $id) }}" class="btn btn-sm btn-outline-warning text-dark" title="Editar Credenciais">
        <i class="bi bi-pencil-square"></i> Editar
    </a>
    <form action="{{ route('doctors.destroy', $id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Deseja realmente remover todo o acesso deste médico ao sistema? Esta ação é irreversível se não usar SoftDeletes!');" class="m-0 p-0 no-ajax">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover Médico"><i class="bi bi-trash"></i> Excluir</button>
    </form>
</div>
