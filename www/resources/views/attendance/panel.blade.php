<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Chamadas - Recepção</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --bg-color: #0f172a;
            --card-color: #1e293b;
            --text-main: #f8fafc;
            --accent-color: #3b82f6;
            --danger-color: #ef4444;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            overflow: hidden; /* Totem não rola tela */
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .totem-header {
            background-color: var(--card-color);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            z-index: 10;
        }
        .header-logo { font-size: 2rem; font-weight: 900; letter-spacing: -1px; }
        .clock-display { font-size: 2.5rem; font-weight: 700; color: var(--accent-color); }

        /* Main Content Grid */
        .totem-body {
            flex-grow: 1;
            display: flex;
            padding: 40px;
            gap: 40px;
        }

        /* Chamada Recente (Esquerda Maior) */
        .current-call {
            flex: 2;
            background: linear-gradient(135deg, var(--card-color) 0%, rgba(30,41,59,0.5) 100%);
            border-radius: 20px;
            border: 2px solid rgba(59, 130, 246, 0.3);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .pulse-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(59, 130, 246, 0.1);
            animation: pulse-bg 2s infinite;
            display: none; /* Ativado via JS */
        }

        @keyframes pulse-bg {
            0% { opacity: 0; }
            50% { opacity: 1; }
            100% { opacity: 0; }
        }

        .lbl-chamada { font-size: 2rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 4px; margin-bottom: 20px;}
        .client-name { font-size: 6rem; font-weight: 900; line-height: 1.1; margin-bottom: 40px; text-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        .room-badge { 
            background-color: var(--danger-color); 
            color: white; 
            font-size: 3rem; 
            padding: 15px 50px; 
            border-radius: 50px; 
            font-weight: 900;
            display: inline-block;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.5);
        }

        /* Histórico Lateral (Direita Menor) */
        .history-list {
            flex: 1;
            background-color: var(--card-color);
            border-radius: 20px;
            padding: 30px;
            display: flex;
            flex-direction: column;
        }

        .history-title { font-size: 1.8rem; font-weight: 700; border-bottom: 2px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 20px; color: #94a3b8; }
        
        .history-item {
            background-color: rgba(0,0,0,0.2);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 5px solid var(--accent-color);
        }
        .hist-name { font-size: 1.8rem; font-weight: 700; margin-bottom: 5px;}
        .hist-locale { font-size: 1.2rem; color: #94a3b8; }

        /* Animação para nova entrada */
        .animate-pop { animation: popIn 0.8s cubic-bezier(0.18, 0.89, 0.32, 1.28) forwards; }
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        
    </style>
</head>
<body>

    <!-- Cabeçalho Totem -->
    <header class="totem-header">
        <div class="header-logo text-white">
            <i class="bi bi-heart-pulse-fill text-danger me-2"></i> {{ \App\Models\Setting::where('key', 'site_title')->value('value') ?? 'Medical Diary' }}
        </div>
        <div class="clock-display" id="clock">00:00:00</div>
    </header>

    <!-- Corpo do Totem -->
    <div class="totem-body">
        
        <!-- Paciente Atual Sendo Chamado -->
        <main class="current-call" id="mainCallBox">
            <div class="pulse-overlay" id="pulseFx"></div>
            
            <div id="callIdle" style="opacity: 0.5;">
                <i class="bi bi-display fa-2x mb-3" style="font-size: 6rem;"></i>
                <h1 class="fw-bold">Aguardando Chamadas...</h1>
            </div>

            <div id="callReady" class="d-none w-100 z-1">
                <div class="lbl-chamada">Por favor, dirija-se ao local</div>
                <div class="client-name" id="currentClient">---</div>
                <div class="room-badge" id="currentRoom">---</div>
                <div class="mt-5 fs-4 text-muted"><i class="bi bi-person-badge me-2"></i>Dr(a). <span id="currentDoctor">---</span></div>
            </div>
        </main>

        <!-- Últimas Chamadas -->
        <aside class="history-list">
            <div class="history-title"><i class="bi bi-clock-history me-2"></i> Chamadas Anteriores</div>
            <div id="historyContainer" class="d-flex flex-column" style="overflow: hidden;">
                <!-- Itens injetados via JS -->
            </div>
        </aside>

    </div>

    <!-- Tocar audio oculto (Browser bloqueia som autoplay, o primeiro depende de clique, caso precise ativador nativo) -->
    <!-- Como isso seria pra uma TV dedicada, navegadores em Kiosk mode permitem som. -->
    
    <script>
        // 1. Relógio da TV
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('pt-BR');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Variáveis de Estado
        let historyArray = [];
        let currentCallId = null;

        // 3. Audio / TTS Engine
        const dingAudio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-software-interface-start-2574.mp3'); // Um bell agradável livre online
        
        function speakAlert(name, room) {
            dingAudio.play().catch(e => console.log('Autoplay audio blocked by browser policies unless interacted first.'));
            
            // Voz Robótica Sintetizada (Se navegador suportar TTS no display TV)
            if ('speechSynthesis' in window) {
                setTimeout(() => {
                    const utterance = new SpeechSynthesisUtterance(`Atenção, paciente: ${name}. Comparecer à: ${room}.`);
                    utterance.lang = 'pt-BR';
                    utterance.rate = 0.85; // Fala um pouco mais lenta
                    window.speechSynthesis.speak(utterance);
                }, 1000);
            }
        }

        // 4. Conexão SSE (Eventsource Backend Laravel)
        const evtSource = new EventSource("{{ route('attendance.stream') }}");

        evtSource.addEventListener("NewCall", function(event) {
            const data = JSON.parse(event.data);

            // Verifica se não é a mesma chamada ecoando
            if (currentCallId === data.id) return;
            
            // Se tinha alguém sendo chamado antes, joga pro histórico de TV
            const oldName = document.getElementById('currentClient').innerText;
            const oldRoom = document.getElementById('currentRoom').innerText;
            if(oldName !== '---' && document.getElementById('callReady').classList.contains('d-none') === false) {
                addToHistory(oldName, oldRoom);
            }

            currentCallId = data.id;

            // Renderiza no Box Principal
            document.getElementById('callIdle').classList.add('d-none');
            const boxReady = document.getElementById('callReady');
            boxReady.classList.remove('d-none');
            
            document.getElementById('currentClient').innerText = data.client_name;
            document.getElementById('currentRoom').innerText = data.room;
            document.getElementById('currentDoctor').innerText = data.doctor_name;

            // Roda as animações pular / piscar
            boxReady.classList.remove('animate-pop');
            void boxReady.offsetWidth; // Trigger reflow da animacao
            boxReady.classList.add('animate-pop');

            const pulseFx = document.getElementById('pulseFx');
            pulseFx.style.display = 'block';
            setTimeout(() => { pulseFx.style.display = 'none'; }, 6000); // Pisca por 6s e depois para

            // Toca Sons
            speakAlert(data.client_name, data.room);
            
        });

        evtSource.onerror = function(err) {
            console.error("Conexão SSE perdida com o servidor. O Navegador tentará reconexão automática...", err);
        };

        // 5. Motor Visual Histórico
        function addToHistory(name, room) {
            const container = document.getElementById('historyContainer');
            const item = document.createElement('div');
            item.className = 'history-item animate-pop';
            item.innerHTML = `
                <div class="hist-name">${name}</div>
                <div class="hist-locale"><i class="bi bi-geo-alt-fill me-1"></i> ${room}</div>
            `;
            
            container.prepend(item);

            // Mantém apenas os últimos 5
            if(container.children.length > 5) {
                container.lastElementChild.remove();
            }
        }

        // Mapear vozes ao carregar
        window.speechSynthesis.onvoiceschanged = function() {
            window.speechSynthesis.getVoices();
        };

        // Trick nativo de clique oculto para habilitar Audio no browser Moderno
        // O administrador fará 1 (um) clique na tela e ela nunca mais bloqueia som.
        document.body.addEventListener('click', () => {
            if(dingAudio.paused && !dingAudio.src) { dingAudio.load(); }
            
            // Força a síntese acordar para o navegador não bloquear o TTS em segundo plano depois
            if('speechSynthesis' in window) {
                let utterance = new SpeechSynthesisUtterance('');
                utterance.volume = 0;
                window.speechSynthesis.speak(utterance);
            }
        }, {once:true});

    </script>
</body>
</html>
