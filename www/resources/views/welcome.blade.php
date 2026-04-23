<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuidado Excepcional - Medical Diary</title>
    <!-- Tailwind via CDN apenas para a Landing Page isolada para agilizar design bonito ou puramente Bootstrap conforme projeto. 
         Usaremos Bootstrap para manter padrão com o sistema. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; overflow-x: hidden; }
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 100px 0;
            position: relative;
        }
        .hero h1 { font-size: 3.5rem; font-weight: 800; letter-spacing: -1px; }
        .hero p { font-size: 1.2rem; opacity: 0.9; }
        .features { padding: 80px 0; background: #f8fafc; }
        .feature-card {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 30px;
            transition: transform 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            height: 100%;
        }
        .feature-card:hover { transform: translateY(-5px); }
        .icon-box {
            width: 60px; height: 60px;
            background: #e0e7ff;
            color: #4f46e5;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 20px;
        }
        .navbar-custom { background: rgba(15, 23, 42, 0.95); padding: 15px 0; }
    </style>
</head>
<body>

    <!-- Navbar Base -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="#">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Medical Diary
            </a>
            <div class="d-flex">
                <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4 rounded-pill">Acessar Plataforma</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1>Sua Saúde Mapeada Digitalmente.</h1>
                    <p class="mt-4 mb-5">
                        Consultas simplificadas, prontuário seguro em Nuvem e comunicação direta. Bem-vindo à evolução da saúde conectada para pacientes e médicos experientes.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-bold rounded-pill text-primary px-5 py-3">Área do Paciente</a>
                </div>
                <div class="col-lg-6 text-center">
                    <!-- Placeholder premium design -->
                    <div style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-shield-lock text-success" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Acesso 100% Protegido</h3>
                        <p class="opacity-75">Suas informações clínicas contam com criptografia de ponta a ponta e rígido isolamento de banco de dados.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Funcionalidades -->
    <section class="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nossos Serviços</h2>
                <p class="text-muted">A infraestrutura que cuida de você de ponta a ponta.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box"><i class="bi bi-calendar-check"></i></div>
                        <h4>Agendamento Smart</h4>
                        <p class="text-muted">Descomplique sua agenda. Reduza faltas usando nosso sistema integrado de lembretes automatizados.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="bi bi-file-medical"></i></div>
                        <h4>Prontuário Único</h4>
                        <p class="text-muted">O médico possui acesso rápido a seu histórico inteiro, gerando receitas tokenizadas num passe de mágica.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-wallet2"></i></div>
                        <h4>Extrato Financeiro</h4>
                        <p class="text-muted">Área do portal do cliente com relatórios, faturas pendentes e transparência de custos de forma unificada.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 opacity-75">
        <div class="container">
            <p class="mb-0">© 2026 Medical Diary Inc. - Plataforma de Gestão em Saúde.</p>
        </div>
    </footer>

</body>
</html>
