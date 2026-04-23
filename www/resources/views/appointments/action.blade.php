<!-- Arquivo de botões de Ações do Datatable para Appointments -->
<button class="btn btn-sm btn-outline-info" title="Visualizar Prontuário">
    <i class="bi bi-eye"></i>
</button>

<form action="{{ route('attendance.call', $id) }}" method="POST" class="d-inline" onsubmit="return handleCall(event)">
    @csrf
    <button type="submit" class="btn btn-sm btn-outline-success" title="Chamar no Painel">
        <i class="bi bi-display"></i>
    </button>
</form>

<button class="btn btn-sm btn-outline-primary" title="Editar">
    <i class="bi bi-pencil"></i>
</button>
<button class="btn btn-sm btn-outline-danger" title="Cancelar Agendamento">
    <i class="bi bi-x-circle"></i>
</button>

<script>
    function handleCall(e) {
        // Confirmação via SweetAlert ou nativo caso deseje futuramente.
        // O app.ts já vai capturar o onsubmit e exibir toasts
        return true; 
    }
</script>
