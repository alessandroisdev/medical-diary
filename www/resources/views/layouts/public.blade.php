<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ $settings['site_title'] ?? 'Medical Diary' }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; overflow-x: hidden; }
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 80px 0;
            position: relative;
        }
        .hero h1 { font-size: 3.2rem; font-weight: 800; letter-spacing: -1px; }
        .hero p { font-size: 1.2rem; opacity: 0.9; }
        .section-padding { padding: 90px 0; }
        .bg-light-subtle { background: #f8fafc; }
        
        .navbar-custom { background: rgba(15, 23, 42, 0.98); padding: 15px 0; backdrop-filter: blur(10px); }
        .nav-link { color: rgba(255,255,255,0.8) !important; font-weight: 500; transition: color 0.3s; margin: 0 5px; }
        .nav-link:hover, .nav-link.active { color: white !important; }
        
        .card-specialty { border-radius: 12px; border: none; transition: transform 0.3s ease; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .card-specialty:hover { transform: translateY(-5px); }
        
        .avatar-doc { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #e0e7ff; }

        .page-header { background: #0f172a; padding: 60px 0; color: white; margin-bottom: 40px;}
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-white" href="{{ route('home') }}">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Medical Diary
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navItems">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navItems">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-2">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Início</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.about') ? 'active' : '' }}" href="{{ route('public.about') }}">Nossa Infra</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.specialties') ? 'active' : '' }}" href="{{ route('public.specialties') }}">Especialidades</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.doctors') ? 'active' : '' }}" href="{{ route('public.doctors') }}">Corpo Clínico</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.contact') ? 'active' : '' }}" href="{{ route('public.contact') }}">Local e Contato</a></li>
                </ul>
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm"><i class="bi bi-person-fill me-1"></i> Área do Cliente</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 opacity-75 mt-auto">
        <div class="container">
            <p class="mb-0">© 2026 Medical Diary Inc. - Plataforma Institucional e Assistencial.</p>
             <div class="d-flex justify-content-center gap-3 mt-2">
                @if(!empty($settings['social_instagram']))
                    <a href="{{ $settings['social_instagram'] }}" class="text-white opacity-75"><i class="bi bi-instagram fs-5"></i></a>
                @endif
             </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @stack('scripts')
</body>
</html>
