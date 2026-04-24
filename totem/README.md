# Totem de Senhas - Medical Diary 🎟️

Este é o microserviço e Client UI isolado para rodar nativamente em cima da máquina local da recepção (ou totem em tela cheia) que fica fisicamente ligada à impressora Térmica via USB ou Rede LAN. Utilizamos tecnologias modernas de Node.js + NPM para interceptar o driver C++ da impressora Windows.

## Pré-requisitos na Maquina Kiosk/Totem
1. Ter o **Node.js 18+** Instalado no Windows.
2. Instalar o driver original da sua Impressora Térmica e certificar-se de ter dado um "Print Test Page" validando o funcionamento no spooler do Windows.
3. Compartilhar a impressora no Windows e notar qual nome você deu para a porta.

## Configuração
Abra o arquivo `server.js` na raiz e ajuste as duas variáveis principais no topo:
```javascript
// URL onde seu Docker App está exposto (pode usar o IP Local da Máquina Host do Docker se for em rede)
const LARAVEL_API_URL = 'http://localhost:8084/api/tickets/generate'; 

// O nome da Impressora Térmica na sua rede Local (UNC Path)
const PRINTER_NAME = 'EPSON_TM_T20'; // ex: '\\\\localhost\\EPSON_TM_T20'
const IS_PRODUCTION = true; // Coloque 'true' quando espetar a máquina
```

## Como Rodar

Abra o PowerShell dentro desta pasta raiz `/totem` e rode:
1. `npm install` (Para baixar a api serial térmica nativa)
2. `npm start` (Para ligar o daemon local na subporta :8085)

Em seguida, ponha seu navegador (Chrome/Edge) em tela cheia via `F11` (Kiosk) na mesma máquina, acessando `http://localhost:8085`.
Pronto! Os botões acionarão a engrenagem, emitirão o comando pra API de Docker do Backend, e queimarão o papel no Totem.
