<div class="btn-group" role="group">
    <!-- Delete JS Button Logic handled via global confirmation (assuming we build one) or form -->
    <form action="{{ route('doctors.destroy', $id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Deseja realmente remover todo o acesso deste médico ao sistema? Esta ação é irreversível se não usar SoftDeletes!');" class="m-0 p-0">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover Médico"><i class="bi bi-trash"></i> Excluir</button>
    </form>
</div>
