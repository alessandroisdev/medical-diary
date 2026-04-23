<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Chamadas - Medical Diary</title>
    
    @vite(['resources/sass/app.scss'])
    
    <style>
        body { background-color: #0b1c3d; color: white; overflow: hidden; }
        .panel-container { height: 100vh; display: flex; flex-direction: column; }
        .header { background: #0044cc; padding: 20px; text-align: center; font-size: 2rem; font-weight: bold; border-bottom: 5px solid #0099ff; }
        .content { flex: 1; display: flex; align-items: center; justify-content: center; text-align: center; flex-direction: column; }
        
        .patient-name { font-size: 8rem; font-weight: 900; line-height: 1; text-transform: uppercase; color: #ffeb3b; 
                        text-shadow: 4px 4px 10px rgba(0,0,0,0.5); }
        .doctor-name { font-size: 3rem; margin-top: 20px; font-weight: 500; }
        .room-text { font-size: 4rem; margin-top: 40px; font-weight: bold; background: #e91e63; padding: 10px 40px; border-radius: 50px; 
                     box-shadow: 0px 10px 20px rgba(0,0,0,0.3); }
        
        .footer { background: #0e2759; padding: 15px; font-size: 1.5rem; display: flex; justify-content: space-between; }
        
        /* Piscar tela inicial na chamada */
        @keyframes flash {
            0% { background-color: #fff; }
            100% { background-color: #0b1c3d; }
        }
        .flash-active { animation: flash 1.5s ease-out; }
    </style>
</head>
<body>

<div class="panel-container" id="screen">
    <div class="header">
        <i class="bi bi-display"></i> SISTEMA DE CHAMADA - MEDICAL DIARY
    </div>
    
    <div class="content">
        <div id="waitForCall">
            <i class="bi bi-person-video2" style="font-size: 10rem; opacity: 0.3;"></i>
            <h2 style="opacity: 0.5;">Aguardando Próxima Chamada...</h2>
        </div>

        <div id="callData" style="display: none;">
            <div class="patient-name" id="nameValue">-</div>
            <div class="doctor-name">Médico(a): <span id="doctorValue">-</span></div>
            <div class="room-text">Dirija-se à: <span id="roomValue">-</span></div>
        </div>
    </div>
    
    <div class="footer">
        <div><i class="bi bi-info-circle"></i> Fique atento ao painel.</div>
        <div id="clock">--:--:--</div>
    </div>
</div>

<script>
    // Relógio basico
    setInterval(() => {
        const d = new Date();
        document.getElementById('clock').innerText = d.toLocaleTimeString('pt-BR');
    }, 1000);

    // Conecta ao fluxo Server-Sent Events
    const sseUrl = '{{ route('attendance.stream') }}';
    
    // Suporte nativo ao EventSource no Javascript
    const eventSource = new EventSource(sseUrl);
    
    // Tratando eventos recebidos do PHP
    eventSource.addEventListener('NewCall', function(event) {
        try {
            const data = JSON.parse(event.data);
            
            // Toque sonoro sintético simples (precisa haver interação prévia na tela as vezes)
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(800, audioCtx.currentTime); // Tom da chamada
            gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            oscillator.start();
            setTimeout(() => oscillator.stop(), 800); // Toca bip por 800ms
            
            // Ativa flash effect
            const screen = document.getElementById('screen');
            screen.classList.remove('flash-active');
            void screen.offsetWidth; // trigger reflow
            screen.classList.add('flash-active');

            // Exibe na tela
            document.getElementById('waitForCall').style.display = 'none';
            document.getElementById('callData').style.display = 'block';
            
            document.getElementById('nameValue').innerText = data.client_name;
            document.getElementById('doctorValue').innerText = data.doctor_name;
            document.getElementById('roomValue').innerText = data.room;
            
        } catch(e) {
            console.error("Erro no Parse SSE", e);
        }
    });

    eventSource.onerror = function(err) {
        console.error("SSE Connection Error", err);
        // O JS tenta reconectar automaticamente
    };
</script>

</body>
</html>
