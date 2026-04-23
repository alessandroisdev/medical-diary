<div class="btn-group" role="group">
    <form action="{{ route('collaborators.destroy', $id) }}" method="POST" onsubmit="return confirm('Tem certeza? Isso remove todo o acesso ao painel principal.');" class="m-0 p-0">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Revogar Acesso"><i class="bi bi-trash"></i> Revogar</button>
    </form>
</div>
