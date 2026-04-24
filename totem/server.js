const express = require('express');
const cors = require('cors');
const axios = require('axios');
const path = require('path');
const ThermalPrinter = require("node-thermal-printer").printer;
const PrinterTypes = require("node-thermal-printer").types;
const { exec } = require('child_process');

const fs = require('fs');

const app = express();
app.use(cors());
app.use(express.json());
app.use(express.static('public'));

// Configuração do Ambiente
const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://localhost:8084/api/tickets/generate';
const CONFIG_FILE = path.join(__dirname, 'hardware_config.json');

// Lê configurações locais ou retorna chaves padrão
function loadConfig() {
    if (fs.existsSync(CONFIG_FILE)) {
        try { return JSON.parse(fs.readFileSync(CONFIG_FILE, 'utf8')); } 
        catch (e) { console.error("Erro lendo json:", e); }
    }
    return { printer_name: 'Comum_Impressora_Ficticia', is_production: false };
}

// Persiste no Cofre JSON local
function saveConfig(data) {
    fs.writeFileSync(CONFIG_FILE, JSON.stringify(data, null, 4), 'utf8');
}

// Endpoints Administrativos Ocultos
app.get('/totem/config', (req, res) => { res.json(loadConfig()); });
app.post('/totem/config', (req, res) => {
    const current = loadConfig();
    const newData = { ...current, ...req.body };
    saveConfig(newData);
    res.json({ success: true, message: 'Configurações Vivas Salvas!' });
});
app.get('/totem/printers', (req, res) => {
    exec('powershell -Command "Get-Printer | Select-Object Name | ConvertTo-Json"', (err, stdout, stderr) => {
        if (err) return res.json([]);
        try {
            if(!stdout || stdout.trim() === '') return res.json([]);
            let data = JSON.parse(stdout);
            if (!Array.isArray(data)) data = [data]; // Trata caso aja apenas 1 (Vira objeto simples)
            const names = data.map(p => p.Name).filter(Boolean);
            res.json(names);
        } catch(e) { res.json([]); }
    });
});

app.post('/totem/request', async (req, res) => {
    try {
        const type = req.body.type;
        
        // 1. Requisitar a numeração viva do Banco de Dados no Laravel
        const apiResponse = await axios.post(LARAVEL_API_URL, { type: type });
        const ticketData = apiResponse.data; // { ticket: 'P001', message: '...' }

        // 2. Acionar a Impressora Localmente
        await printTicket(ticketData.ticket, type);

        res.json({ success: true, ticket: ticketData.ticket });
    } catch (error) {
        console.error("Erro ao gerar/imprimir senha", error.message);
        res.status(500).json({ error: "Falha na comunicação." });
    }
});

async function printTicket(ticketStr, typeStr) {
    const sysData = loadConfig();

    if (!sysData.is_production || sysData.is_production === 'false') {
        console.log(`[DEV MODE] Imprimindo MOCK no Totem: Senha ${ticketStr} (${typeStr}) [Driver: ${sysData.printer_name}]`);
        return;
    }

    let printer = new ThermalPrinter({
        type: PrinterTypes.EPSON,
        interface: 'printer:' + sysData.printer_name, 
        characterSet: 'PC858_EURO', // Letras com acento (pt-br)
        removeSpecialCharacters: false,
    });

    printer.alignCenter();
    printer.println("MEDICAL DIARY - BEM VINDO!");
    printer.println("--------------------------------");
    printer.setTextSize(2, 2);
    printer.println("SENHA");
    printer.setTextSize(6, 6);
    printer.println(ticketStr);
    printer.setTextSize(1, 1);
    printer.println("--------------------------------");
    
    if(typeStr === 'priority') {
        printer.println("ATENDIMENTO PREFERENCIAL");
    } else {
        printer.println("ATENDIMENTO COMUM");
    }
    
    printer.println("Aguarde a chamada pela TV...");
    printer.println(new Date().toLocaleString('pt-BR'));
    printer.cut();

    try {
        await printer.execute();
        console.log("Impressão Física enviada para fila USB com Sucesso.");
    } catch (error) {
        console.error("Erro físico da Impressora:", error);
    }
}

app.listen(8085, () => {
    console.log("Servidor Totem Local Iniciado!");
    console.log("Acesse: http://localhost:8085 no Chrome (Kiosk Mode) para operar o Totem.");
});
