<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medical Diary')</title>
    
    @vite(['resources/sass/app.scss', 'resources/ts/app.ts'])
    
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) { registration.unregister(); }
            });
        }
    </script>
    
    <!-- Datatables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-heart-pulse-fill me-2"></i>Medical Diary
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navItems">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navItems">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                    @auth('collaborator')
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-1"></i> O.S. (Agendamentos)</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white fw-bold" href="{{ route('reception.queue') }}"><i class="bi bi-ticket-perforated-fill text-warning me-1"></i> Totem Recepção</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white" href="{{ route('attendance.panel') }}" target="_blank"><i class="bi bi-display me-1"></i> TV Salão</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white fw-bold" href="{{ route('schedules.index') }}"><i class="bi bi-calendar-range text-info me-1"></i> Monitor. Escalas</a>
                    </li>
                    @endauth

                    @auth('admin')
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('transactions.index') }}"><i class="bi bi-cash-coin me-1"></i> DRE Financeiro</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white fw-bold" href="{{ route('inbox.index') }}"><i class="bi bi-headset text-warning me-1"></i> Contato Site</a>
                    </li>
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="rhDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-diagram-3-fill me-1"></i> ERP Clinica
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="rhDropdown">
                            <li><a class="dropdown-item" href="{{ route('doctors.index') }}"><i class="bi bi-person-heart me-2 text-primary"></i>Corpo Clínico (Médicos)</a></li>
                            <li><a class="dropdown-item" href="{{ route('collaborators.index') }}"><i class="bi bi-person-badge me-2 text-primary"></i>Recepção (Atendentes)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Estrutura de Mercado</h6></li>
                            <li><a class="dropdown-item" href="{{ route('specialties.index') }}"><i class="bi bi-tags me-2 text-warning"></i>Especialidades Clínicas</a></li>
                            <li><a class="dropdown-item" href="{{ route('health-insurances.index') }}"><i class="bi bi-shield-check me-2 text-success"></i>Convênios / Acordos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('schedules.index') }}"><i class="bi bi-calendar-range me-2 text-danger"></i>Relatório Escalas Médicas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-secondary fw-bold" href="{{ route('settings.index') }}"><i class="bi bi-gear-fill me-2"></i>Configurações Mestre</a></li>
                        </ul>
                    </li>
                    @endauth

                    @auth('doctor')
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="{{ route('records.index') }}"><i class="bi bi-file-medical-fill text-info me-1"></i> Prontuário Pacientes</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white" href="{{ route('appointments.index') }}"><i class="bi bi-person-lines-fill text-warning me-1"></i> Fila Operacional (Hoje)</a>
                    </li>
                    @endauth
                    
                    @auth('client')
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('portal.index') }}"><i class="bi bi-person-vcard text-info me-1"></i> Meu Portal SaaS</a>
                    </li>
                    @endauth
                </ul>
                <div class="d-flex text-white align-items-center">
                    @if(Auth::guard('admin')->check())
                        <span class="badge bg-danger me-2">ADMIN</span> {{ Auth::guard('admin')->user()->name }}
                    @elseif(Auth::guard('doctor')->check())
                        @php $doc = Auth::guard('doctor')->user(); @endphp
                        @if($doc->current_room)
                            <button type="button" class="btn btn-sm btn-success me-2 rounded-pill shadow-sm" onclick="promptDoctorRoom('{{ $doc->current_room }}')">
                                <i class="bi bi-geo-alt-fill me-1"></i> {{ $doc->current_room }}
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-danger me-2 rounded-pill shadow-sm" onclick="promptDoctorRoom('')">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Fazer Check-In na Sala
                            </button>
                        @endif
                        <span class="badge bg-success me-2 align-self-center">MÉDICO</span> <span class="align-self-center">{{ $doc->name }}</span>
                    @elseif(Auth::guard('collaborator')->check())
                        @php $collab = Auth::guard('collaborator')->user(); @endphp
                        @if($collab->current_room)
                            <button type="button" class="btn btn-sm btn-success me-2 rounded-pill shadow-sm" onclick="promptCollaboratorRoom('{{ $collab->current_room }}')">
                                <i class="bi bi-geo-alt-fill me-1"></i> Guichê {{ $collab->current_room }}
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-danger me-2 rounded-pill shadow-sm" onclick="promptCollaboratorRoom('')">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Check-in no Guichê
                            </button>
                        @endif
                        <span class="badge bg-info text-dark me-2 align-self-center">RECEPÇÃO</span> <span class="align-self-center">{{ $collab->name }}</span>
                    @elseif(Auth::guard('client')->check())
                        <span class="badge bg-primary me-2">PACIENTE</span> {{ Auth::guard('client')->user()->name }}
                    @endif
                    
                    <a href="{{ route('help.generic') }}" class="btn btn-sm btn-info text-dark fw-bold ms-3" title="Base de Conhecimento">
                        <i class="bi bi-question-circle-fill me-1"></i> Ajuda
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="no-ajax ms-2 m-0 p-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Toast Container Limitado -->
        <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060"></div>
        
        @yield('content')
    </div>

    <!-- Script de Fallback e Jquery/Datatables -->
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
    
    @auth('doctor')
    <script>
        function promptDoctorRoom(currentRoom) {
            Swal.fire({
                title: currentRoom ? 'Mudar Sala de Atendimento' : 'Fazer Check-In',
                input: 'text',
                inputValue: currentRoom,
                inputLabel: 'Qual a sua Sala/Consultório atualmente?',
                inputPlaceholder: 'Ex: Consultório 3',
                icon: 'hospital',
                showCancelButton: true,
                showDenyButton: currentRoom ? true : false,
                denyButtonText: 'Sair da Sala (Check-Out)',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if(!result.value && !currentRoom) return; // ignore empty
                    axios.post('{{ route("doctor.room.update") }}', { room: result.value })
                         .then(res => { window.location.reload(); })
                         .catch(e => { Swal.fire('Erro', 'Ocorreu um erro ao salvar o local', 'error'); });
                } else if (result.isDenied) {
                    axios.post('{{ route("doctor.room.update") }}', { room: null }).then(res => { window.location.reload(); });
                }
            });
        }
    </script>
    @endauth

    @auth('collaborator')
    <script>
        function promptCollaboratorRoom(currentRoom) {
            Swal.fire({
                title: currentRoom ? 'Trocar de Guichê' : 'Check-In na Fila',
                input: 'text',
                inputValue: currentRoom,
                inputLabel: 'Qual o Número do seu Guichê?',
                inputPlaceholder: 'Ex: 1, 2, Triagem',
                icon: 'info',
                showCancelButton: true,
                showDenyButton: currentRoom ? true : false,
                denyButtonText: 'Sair do Guichê',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if(!result.value && !currentRoom) return; 
                    axios.post('{{ route("collaborator.room.update") }}', { room: result.value })
                         .then(res => { window.location.reload(); })
                         .catch(e => { Swal.fire('Erro', 'Ocorreu um erro ao salvar', 'error'); });
                } else if (result.isDenied) {
                    axios.post('{{ route("collaborator.room.update") }}', { room: null }).then(res => { window.location.reload(); });
                }
            });
        }
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
