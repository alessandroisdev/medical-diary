<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medical Diary')</title>
    
    @vite(['resources/sass/app.scss', 'resources/ts/app.ts'])
    
    <!-- Datatables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
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
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth('collaborator')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('appointments.index') }}">Agendamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendance.panel') }}">Controle de Senhas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="{{ route('schedules.index') }}"><i class="bi bi-calendar-range me-1"></i>Escalas Diárias</a>
                    </li>
                    @endauth

                    @auth('admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('transactions.index') }}">Financeiro Geral</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="rhDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-people-fill me-1"></i> Gestão e RH
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="rhDropdown">
                            <li><a class="dropdown-item" href="{{ route('doctors.index') }}"><i class="bi bi-person-heart me-2"></i>Corpo Clínico (Médicos)</a></li>
                            <li><a class="dropdown-item" href="{{ route('collaborators.index') }}"><i class="bi bi-person-badge me-2"></i>Recepção (Atendentes)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('schedules.index') }}"><i class="bi bi-calendar-range me-2"></i>Escala e Agendas Médico</a></li>
                        </ul>
                    </li>
                    @endauth

                    @auth('doctor')
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="{{ route('records.index') }}">Prontuário Médico</a>
                    </li>
                    @endauth
                    
                    @auth('client')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('portal.index') }}">Meu Portal</a>
                    </li>
                    @endauth
                </ul>
                <div class="d-flex text-white align-items-center">
                    @if(Auth::guard('admin')->check())
                        <span class="badge bg-danger me-2">ADMIN</span> {{ Auth::guard('admin')->user()->name }}
                    @elseif(Auth::guard('doctor')->check())
                        <span class="badge bg-success me-2">MÉDICO</span> {{ Auth::guard('doctor')->user()->name }}
                    @elseif(Auth::guard('collaborator')->check())
                        <span class="badge bg-info text-dark me-2">RECEPÇÃO</span> {{ Auth::guard('collaborator')->user()->name }}
                    @elseif(Auth::guard('client')->check())
                        <span class="badge bg-primary me-2">PACIENTE</span> {{ Auth::guard('client')->user()->name }}
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" class="no-ajax ms-3 m-0 p-0">
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
    
    @stack('scripts')
</body>
</html>
