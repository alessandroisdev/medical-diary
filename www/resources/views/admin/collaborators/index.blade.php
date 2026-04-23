@extends('layouts.app')

@section('title', 'Gestão de Atendentes - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-person-badge me-2"></i>Gestão de Atendentes (Recepção)</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCollaboratorModal">
                <i class="bi bi-plus-circle me-1"></i> Novo Atendente
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
<div class="modal fade" id="createCollaboratorModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('collaborators.store') }}" method="POST" id="formCreateColl">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Novo Acesso de Recepção</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail (Acesso ao Sistema)</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Senha Inicial Provisória</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <div class="form-text">Mínimo de 6 caracteres. O atendente usará isso no painel central.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold" data-original-text="Cadastrar Atendente">Cadastrar Atendente</button>
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
            const formObj = document.getElementById('formCreateColl');
            if(formObj) {
                formObj.addEventListener('submit', () => {
                    setTimeout(() => {
                        if(window.LaravelDataTables && window.LaravelDataTables["collaborator-table"]) {
                            window.LaravelDataTables["collaborator-table"].ajax.reload(null, false);
                            
                            const modalNode = document.getElementById('createCollaboratorModal');
                            if(modalNode){
                                const modalInst = bootstrap.Modal.getInstance(modalNode);
                                if(modalInst) modalInst.hide();
                            }
                        }
                    }, 500); 
                });
            }
        });
    </script>
@endpush
