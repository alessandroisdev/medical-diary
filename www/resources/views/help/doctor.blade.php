@extends('layouts.app')

@section('title', 'Médico - Central de Ajuda')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <h2 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-question-circle text-primary me-2"></i> Ajuda e Tutoriais (Corpo Clínico)</h2>

        <div class="accordion shadow-sm" id="doctorHelp">
            <!-- Check-in e TV -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#col1">
                        <i class="bi bi-geo-alt-fill text-success me-2"></i> Check-in de Salas e Chamada pela TV (1-Click)
                    </button>
                </h2>
                <div id="col1" class="accordion-collapse collapse show" data-bs-parent="#doctorHelp">
                    <div class="accordion-body">
                        <p>O Medical Diary permite que você troque de consultório dinamicamente sem perder rastreabilidade. Para começar a atender as filas de hoje:</p>
                        <ol>
                            <li>Olhe no topo direito da sua tela (Navbar). Se houver um botão vermelho <b>"Fazer Check-In"</b>, clique nele.</li>
                            <li>Informe onde você está locado hoje (ex: <em>"Consultório 3"</em> ou <em>"Sala de Raio-X"</em>).</li>
                            <li>A partir desse momento, ao entrar em <b>Minha Fila de Hoje</b>, o botão <b>"Chamar na TV"</b> para cada paciente na lista enviará um disparo instantâneo pra recepção com a voz de robô e seu nome e sala, sem você precisar digitar mais nada (1-Click Caller)!</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Prontuário -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#col2">
                        <i class="bi bi-file-medical-fill text-danger me-2"></i> Emissão Rápida de Prontuários (Impresso Nativo)
                    </button>
                </h2>
                <div id="col2" class="accordion-collapse collapse" data-bs-parent="#doctorHelp">
                    <div class="accordion-body">
                        <p>A aba de <b>Prontuário Médico (Medical Records)</b> é seu arsenal de diagnósticos.</p>
                        <ul>
                            <li>Ao salvar um prontuário, você notará o botão <b>Imprimir A4</b>. Essa função carrega um Layout especial (invisível em telas pequenas) totalmente timbrado com a logo da clínica e seus dados de CRM no cabeçalho.</li>
                            <li>Basta selecionar a sua impressora Laser ou salvar em PDF; o sistema formata e assina sozinho.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
