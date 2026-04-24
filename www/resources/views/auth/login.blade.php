<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Múltiplo - Medical Diary</title>
    
    @vite(['resources/sass/app.scss', 'resources/ts/app.ts'])
    
    <style>
        body {
            background-color: #f1f5f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 450px;
        }
        .nav-pills .nav-link {
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 0 5px;
            color: #64748b;
        }
        .nav-pills .nav-link.active {
            background-color: #0f172a;
        }
    </style>
</head>
<body>

    <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060"></div>

    <div class="login-box border-top border-primary border-4">
        <h3 class="text-center fw-bold mb-1"><i class="bi bi-heart-pulse-fill text-primary"></i> Medical Diary</h3>
        <p class="text-center text-muted mb-4">{{ $title ?? 'Plataforma de Gestão Clínica' }}</p>

        <div id="loginFormBox">
            <div class="px-2">
                <form action="{{ route('login.post') }}" method="POST" id="authForm">
                    @csrf
                    <!-- Guard Input Fixo pela URI -->
                    <input type="hidden" name="guard" id="selectedGuard" value="{{ $guard ?? 'client' }}">

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Email de Acesso</label>
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="email@exemplo.com" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Senha</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label small" for="rememberMe">Lembrar-me</label>
                        </div>
                        <a href="#" class="text-decoration-none small text-primary">Esqueceu a senha?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold" data-original-text="Acessar Sistema">Acessar Sistema</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script de submissão -->
    <script>

        // Configuração pontual pro XHR do Login caso aprovado
        window.addEventListener('load', function() {
            if(window.axios) {
                const form = document.querySelector('#authForm');
                form.onsubmit = function(evt) {
                    evt.preventDefault();
                    evt.stopImmediatePropagation(); // Pára o interceptor global do app.ts pra tratarmos o redirect
                    
                    const btn = form.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Autenticando...';
                    
                    const fd = new FormData(form);
                    window.axios.post(form.action, Object.fromEntries(fd))
                        .then(res => {
                            // Sucesso (200), joga pra tela
                            window.location.href = res.data.redirect || '/appointments';
                        })
                        .catch(err => {
                            btn.disabled = false;
                            btn.innerHTML = btn.getAttribute('data-original-text');
                            
                            // Chama sweet alert visual
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao Acessar',
                                text: err.response?.data?.message || 'Falha na autenticação.',
                            });
                        });
                    
                    return false;
                };
            }
        });
    </script>
</body>
</html>
