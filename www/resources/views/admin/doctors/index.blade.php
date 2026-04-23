@extends('layouts.app')

@section('title', 'Gestão de Médicos - Medical Diary')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 text-dark"><i class="bi bi-person-heart me-2"></i>Corpo Clínico (Médicos)</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDoctorModal">
                <i class="bi bi-plus-circle me-1"></i> Novo Médico
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
<div class="modal fade" id="createDoctorModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('doctors.store') }}" method="POST" id="formCreateDoctor">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Novo Acesso Médico</h5>
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
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">CRM</label>
                            <input type="text" name="crm" class="form-control" required placeholder="Ex: 12345-SP">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">Especialidade (Opcional)</label>
                            <input type="text" name="specialty" class="form-control" placeholder="Pediatria, Clínica Geral...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Senha Inicial Provisória</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <div class="form-text">Mínimo de 6 caracteres. O médico poderá alterar depois.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold" data-original-text="Cadastrar Médico">Cadastrar Médico</button>
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
            const formObj = document.getElementById('formCreateDoctor');
            if(formObj) {
                formObj.addEventListener('submit', () => {
                    setTimeout(() => {
                        if(window.LaravelDataTables && window.LaravelDataTables["doctor-table"]) {
                            window.LaravelDataTables["doctor-table"].ajax.reload(null, false);
                            
                            const modalNode = document.getElementById('createDoctorModal');
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
