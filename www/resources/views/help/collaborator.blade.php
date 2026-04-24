@extends('layouts.app')

@section('title', 'Recepção - Manuais')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <h2 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-info-square text-info me-2"></i> Base de Conhecimento - Recepção</h2>

        <div class="accordion shadow-sm" id="recepHelp">
            <!-- Totem e TV -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#tab1">
                        <i class="bi bi-ticket-perforated-fill text-success me-2"></i> Chamando Senhas do Totem de Entrada
                    </button>
                </h2>
                <div id="tab1" class="accordion-collapse collapse show" data-bs-parent="#recepHelp">
                    <div class="accordion-body">
                        <p>O paciente retirou um papel (P001 ou C003) no Totem. Seu trabalho no balcão é super simples:</p>
                        <ol>
                            <li>Faça o <b>Check-in Vermelho</b> ali no Topo da Tela informando o número do seu Guichê. Sem Informar, você não conseguirá chamar na TV.</li>
                            <li>Vá na Página <a href="{{ route('reception.queue') }}" class="fw-bold text-success">Totem Senhas</a>. Lá em baixo você vê a fila.</li>
                            <li>Basta clicar no botão Gigante <b>"Chamar Próximo"</b>. A nossa Inteligência Artificial vai embaralhar a Fila Preferencial e Normal para que os idosos sejam atendidos e as filas não travem, exibindo o número com Vozes na TV do Saguão!</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Fila Avançada Médicos -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#tab2">
                        <i class="bi bi-funnel-fill text-primary me-2"></i> Gerindo a Fila Cirúrgica (Painel Avançado)
                    </button>
                </h2>
                <div id="tab2" class="accordion-collapse collapse" data-bs-parent="#recepHelp">
                    <div class="accordion-body">
                        <p>A aba de <b>Agendamentos</b> foi criada para otimizar sua visualização (Ela põe quem chegou de forma urgente no Topo). Se você clicar no topo dela, abrirá nossos Filtros Livres.</p>
                        <ul>
                            <li><b>Pesquisa de Paciente:</b> Ao digitar o nome da "Maria", o banco varre e preenche só os Clicks dela. Dê check-in e clique pra chamar ela na TV.</li>
                            <li>A TV tem proteções que impedem que o som trave. Caso aconteça, veja "TV Pausada" na aba física e basta dar um clique nela para as vozes funcionarem pra sempre.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
