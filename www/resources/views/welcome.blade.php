<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['site_title'] ?? 'Medical Diary - Cuidado Ininterrupto' }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; overflow-x: hidden; scroll-behavior: smooth; }
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 120px 0;
            position: relative;
        }
        .hero h1 { font-size: 3.5rem; font-weight: 800; letter-spacing: -1px; }
        .hero p { font-size: 1.2rem; opacity: 0.9; }
        .section-padding { padding: 90px 0; }
        .bg-light-subtle { background: #f8fafc; }
        
        .navbar-custom { background: rgba(15, 23, 42, 0.95); padding: 15px 0; backdrop-filter: blur(10px); }
        .nav-link { color: rgba(255,255,255,0.8) !important; font-weight: 500; transition: color 0.3s; }
        .nav-link:hover, .nav-link.active { color: white !important; }
        
        .card-specialty { border-radius: 12px; border: none; transition: transform 0.3s ease; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .card-specialty:hover { transform: translateY(-5px); }
        
        .avatar-doc { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #e0e7ff; }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#navbarMain" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true">

    <!-- Navbar -->
    <nav id="navbarMain" class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-white" href="#home">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Medical Diary
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navItems">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navItems">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">
                    <li class="nav-item"><a class="nav-link" href="#home">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sobre">Nossa Infra</a></li>
                    <li class="nav-item"><a class="nav-link" href="#time">Corpo Clínico</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contato">Local e Contato</a></li>
                </ul>
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm"><i class="bi bi-person-fill me-1"></i> Acessar Portal</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1>Sua Saúde Mapeada Digitalmente.</h1>
                    <p class="mt-4 mb-5">
                        Consultas simplificadas, prontuário seguro em Nuvem e comunicação direta. Bem-vindo à evolução da saúde conectada para pacientes e médicos experientes.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-bold rounded-pill text-primary px-5 py-3 shadow">Agendar via Portal</a>
                </div>
                <div class="col-lg-6 text-center">
                    <div style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-shield-lock text-success" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Acesso 100% Protegido</h3>
                        <p class="opacity-75">Suas informações clínicas contam com criptografia de ponta a ponta e rígido isolamento em servidores blindados.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Especialidades / Infra -->
    <section id="sobre" class="section-padding bg-light-subtle">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-primary fw-bold text-uppercase tracking-wider small">Abrangência e Tecnologia</span>
                <h2 class="fw-bold mt-2">Nossas Especialidades Direcionadas</h2>
            </div>
            
            <div class="row g-4 justify-content-center">
                @forelse($specialties as $sp)
                    <div class="col-md-3">
                        <div class="card-specialty bg-white p-4 text-center h-100">
                            <i class="bi bi-diagram-3 text-warning fs-1 mb-3"></i>
                            <h5 class="fw-bold">{{ $sp->name }}</h5>
                            <p class="text-muted small mb-0">{{ $sp->description ?: 'Atendimentos focado em alta complexidade com acompanhamento humano.' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">A estrutura inicial está sendo mapeada pelos gestores.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Corpo Clínico -->
    <section id="time" class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-success fw-bold text-uppercase tracking-wider small">Excelência Médica</span>
                <h2 class="fw-bold mt-2">Em Boas Mãos</h2>
                <p class="text-muted opacity-75">Profissionais selecionados focados no cuidado humanizado e preciso.</p>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($doctors as $doc)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm text-center p-4">
                            <!-- Gravatar simple proxy form email para dar uma foto visual randomizada -->
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($doc->name) }}&background=e0e7ff&color=4f46e5&size=128" class="avatar-doc mx-auto mb-3" alt="{{ $doc->name }}">
                            <h5 class="fw-bold text-dark">{{ $doc->name }}</h5>
                            <span class="badge bg-light text-secondary border mb-2"><i class="bi bi-card-heading me-1"></i> {{ $doc->crm }}</span>
                            <div class="small fw-bold text-primary">
                                {{ $doc->specialties->pluck('name')->implode(', ') ?: 'Clínica Geral' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Sessão Contato, SAC e Mapa -->
    <section id="contato" class="section-padding bg-light-subtle">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Local e Fale Conosco</h2>
                <p class="text-muted">Nosso time de recepção responderá rapidamente a sua solicitação pela Triagem Digital.</p>
            </div>

            <div class="row g-5">
                <!-- Informações e Mapa -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <h4 class="fw-bold mb-4">Mesa de Atendimento</h4>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3">
                                <i class="bi bi-envelope-at-fill text-primary me-2"></i>
                                <strong>Canal Corporativo:</strong><br/>
                                <a href="mailto:{{ $settings['contact_email'] ?? '' }}" class="text-decoration-none text-muted">{{ $settings['contact_email'] ?? 'Aguardando Configuração' }}</a>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-telephone-fill text-primary me-2"></i>
                                <strong>WhatsApp / Recepção:</strong><br/>
                                <span class="text-muted">{{ $settings['contact_phone'] ?? 'Em Implantação' }}</span>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                <strong>Endereço Presencial:</strong><br/>
                                <span class="text-muted">{{ $settings['clinic_address'] ?? 'Cidade' }}</span>
                            </li>
                        </ul>
                        
                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                            @if(!empty($settings['map_iframe_url']))
                                <iframe src="{{ $settings['map_iframe_url'] }}" loading="lazy" frameborder="0" style="border:0;" allowfullscreen></iframe>
                            @else
                                <div class="bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-muted">Mapa Desativado</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Formulário Seguro -->
                <div class="col-lg-7">
                    <div class="card border-0 border-top border-primary border-4 shadow-sm p-4 p-md-5">
                        <h5 class="fw-bold mb-4">Formulário Criptografado (Ticket)</h5>
                        
                        <div id="contactMsgContainer" class="d-none alert alert-success"></div>

                        <form id="contactForm" onsubmit="submitForm(event)">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Seu Nome *</label>
                                    <input type="text" class="form-control" name="name" required placeholder="João da Silva">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">E-mail Principal *</label>
                                    <input type="email" class="form-control" name="email" required placeholder="joao@empresa.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Telefone (Opcional)</label>
                                    <input type="text" class="form-control" name="phone" placeholder="(11) 90000-0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Assunto</label>
                                    <input type="text" class="form-control" name="subject" placeholder="Dúvida sobre Agendamento">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Sua Mensagem *</label>
                                    <textarea class="form-control" name="message" rows="4" required placeholder="Escreva os detalhes com o que precisa de ajuda..."></textarea>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" id="btnSubmitContact" class="btn btn-primary fw-bold px-5 py-2">
                                        <i class="bi bi-send me-2"></i> Enviar a Mensagem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 opacity-75">
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

    <script>
        async function submitForm(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btnSubmitContact');
            const alertBox = document.getElementById('contactMsgContainer');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando Segurança...';
            alertBox.classList.add('d-none');
            alertBox.classList.remove('alert-danger', 'alert-success');

            try {
                const formData = new FormData(form);
                const { data } = await axios.post("{{ route('contact.store') }}", formData, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                
                alertBox.className = 'alert alert-success mt-3';
                alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + data.message;
                form.reset();
            } catch(error) {
                let msg = 'Erro ao enviar. Verifique sua conexão.';
                if(error.response?.status === 429) {
                    msg = 'Muitas requisições. Aguarde um minuto para enviar outra mensagem (Proteção Anti-Spam).';
                } else if(error.response?.data?.message) {
                    msg = error.response.data.message;
                }
                
                alertBox.className = 'alert alert-danger mt-3';
                alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + msg;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send me-2"></i> Enviar a Mensagem';
            }
        }
    </script>
</body>
</html>
