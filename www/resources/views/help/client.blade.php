@extends('layouts.app')

@section('title', 'Paciente - Central de Ajuda')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <h2 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-person-hearts text-primary me-2"></i> Ajuda (Seu Portal do Paciente)</h2>

        <div class="accordion shadow-sm" id="clientHelp">
            <!-- Como Agendar -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#box1">
                        <i class="bi bi-calendar-heart text-success me-2"></i> Como Funciona o Auto-Agendamento (Self-Booking)?
                    </button>
                </h2>
                <div id="box1" class="accordion-collapse collapse show" data-bs-parent="#clientHelp">
                    <div class="accordion-body">
                        <p>Nosso aplicativo é interligado em tempo real. Se você clica em <b>"Novo Agendamento"</b>:</p>
                        <ol>
                            <li>O botão pedirá que você escolha um Médico Doutor ou Doutora.</li>
                            <li>Temos um motor matemático que calcula o dia selecionado e subtrai a carga horária em botõezinhos do horário.</li>
                            <li>Basta você clicar no horário que está verde. Ele já garantirá sua reserva no nosso banco de dados, sem você precisar ligar na Recepção! Em caso de erro "Slot Já Pego", é porque outro paciente clicou um segundo antes de você na sua frente. Tente o próximo.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Cancelamento -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#box2">
                        <i class="bi bi-x-circle text-danger me-2"></i> Posso Cancelar uma Consulta?
                    </button>
                </h2>
                <div id="box2" class="accordion-collapse collapse" data-bs-parent="#clientHelp">
                    <div class="accordion-body">
                        <p>Sim. Se você acessar o <b>"Meu Portal"</b>, sua listagem constará com os horários marcados. Use o botão vermelho <b>"Cancelar Horário"</b>. Ao apertá-lo, nós informamos a recepção de seu cancelamento de maneira instantânea via Log, sem atritos e sem cobranças.</p>
                    </div>
                </div>
            </div>

            <!-- Receitas -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#box3">
                        <i class="bi bi-file-earmark-medical text-primary me-2"></i> E minhas receitas e atestados (Prontuários)?
                    </button>
                </h2>
                <div id="box3" class="accordion-collapse collapse" data-bs-parent="#clientHelp">
                    <div class="accordion-body">
                        <p>Assim que o Médico for alimentando e atualizando digitalmente o seu histórico com atestados, você possuirá um Histórico de Prontuários (Prescriptions) com a opção de Exportar Digitalmente cada folha. Sua saúde na sua própria mão.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
