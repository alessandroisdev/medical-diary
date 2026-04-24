@extends('layouts.app')

@section('title', 'Admin - Base de Conhecimento')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        .help-container, .help-container * { visibility: visible; }
        .help-container { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important;}
        .no-print { display: none !important; }
    }
    .topic-icon { font-size: 2rem; color: var(--bs-primary); margin-right: 15px; }
</style>

<div class="row help-container">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold mb-0"><i class="bi bi-journal-medical me-2 text-danger"></i> Manual do Administrador SaaS</h2>
            <div>
                <button class="btn btn-outline-secondary btn-sm no-print me-2" onclick="window.print()"><i class="bi bi-printer-fill me-1"></i> Imprimir PDF</button>
            </div>
        </div>

        <!-- Section 1: TVs e Totens -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h4 class="fw-bold d-flex align-items-center"><i class="bi bi-display topic-icon"></i> Configuração de Monitores (TV e Totem)</h4>
            </div>
            <div class="card-body">
                <p>O Sistema de senhas e autoatendimento roda de forma isolada, permitindo que você espete Monitores Grandes no teto da recepção e Totens Touch-screen na entrada.</p>
                
                <div class="alert alert-dark mb-4">
                    <h6 class="fw-bold text-warning"><i class="bi bi-tv-fill me-1"></i> URL da TV do Saguão</h6>
                    <p class="mb-2 small">Abra o Google Chrome na TV e coloque em Tela Cheia (F11) no seguinte link acessível de qualquer máquina na rede:</p>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light" id="tvUrl" value="{{ url('/attendance') }}" readonly>
                        <button class="btn btn-secondary no-print" type="button" onclick="copyToClipboard('tvUrl', this)"><i class="bi bi-clipboard"></i> Copiar</button>
                    </div>
                    <small class="text-danger mt-2 d-block"><b>Atenção:</b> Você deve plugar um mouse ou controle e dar UM CLIQUE obrigatório no meio da Tela Preta da TV para o Chrome autorizar a "voz do robô TTS".</small>
                </div>

                <div class="alert alert-dark">
                    <h6 class="fw-bold text-info"><i class="bi bi-receipt me-1"></i> URL do Totem de Impressão Node.Js</h6>
                    <p class="mb-2 small">
                        O Totem (Aparelho do papel) roda isolado do Laravel via Node.js.<br/>
                        <strong class="text-white"><i class="bi bi-keyboard text-warning"></i> Painel de Configuração Oculto:</strong> Para conectar e gravar o nome da Impressora de Papel do Windows no sistema local, vá até o Computador do Totem e pressione a combinação secreta <code class="bg-dark text-warning">Ctrl + Alt + P</code> no teclado. Um menu saltará na tela permitindo salvar nativamente.
                    </p>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light" id="totemUrl" value="{{ preg_replace('/:[0-9]+$/', '', request()->getSchemeAndHttpHost()) }}:8085" readonly>
                        <button class="btn btn-secondary no-print" type="button" onclick="copyToClipboard('totemUrl', this)"><i class="bi bi-clipboard"></i> Copiar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Fila Logística -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h4 class="fw-bold d-flex align-items-center"><i class="bi bi-cpu topic-icon"></i> Motor Matemático da Recepção (Pesos de Fila)</h4>
            </div>
            <div class="card-body">
                <p>Quando a recepcionista aperta <b>"Chamar Próximo"</b>, o sistema utiliza uma proporção cravada no Banco de Dados para evitar que as filas normais parem no tempo devido às Prioridades.</p>
                <ul>
                    <li>A proporção Padrão é de <b>2 Preferenciais (P) para 1 Comum (C)</b>.</li>
                    <li>Para editar essa métrica e deixar (Exemplo: 4 pra 1), acesse <b>Configurações Globais</b> no menu e edite as chaves <code>ticket_ratio_priority</code> e <code>ticket_ratio_common</code>.</li>
                </ul>
            </div>
        </div>

        <!-- Section 3: RH e Contabilidade -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h4 class="fw-bold d-flex align-items-center"><i class="bi bi-safe2 topic-icon"></i> Segurança Financeira (LGPD e Contabilidade)</h4>
            </div>
            <div class="card-body">
                <p>O Medical Diary blinda seu Livro-Caixa automaticamente:</p>
                <ul>
                    <li>Transações e Fechamentos Contábeis do dia <b>nunca são apagados (Hard Delete)</b> do HD.</li>
                    <li>O recurso de Cancelamento ou Estorno insere uma linha negativa mascarada para que a métrica do seu Dashobard Financeiro bata centavo por centavo todo fim de mês.</li>
                    <li>Os cadastros de Funcionários/Médicos que forem demitidos apenas "Desaparecem" (Soft Delete) das listas da recepção, mas seus Prontuários históricos e carimbos médicos continuam assinados criptograficamente.</li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(inputId, btnEl) {
        var copyText = document.getElementById(inputId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Mobile
        navigator.clipboard.writeText(copyText.value);

        const oldHtml = btnEl.innerHTML;
        btnEl.innerHTML = '<i class="bi bi-check-lg"></i> Copiado!';
        btnEl.classList.replace('btn-secondary', 'btn-success');
        setTimeout(() => {
            btnEl.innerHTML = oldHtml;
            btnEl.classList.replace('btn-success', 'btn-secondary');
        }, 2000);
    }
</script>
@endpush
