const express = require('express');
const cors = require('cors');
const axios = require('axios');
const path = require('path');
const ThermalPrinter = require("node-thermal-printer").printer;
const PrinterTypes = require("node-thermal-printer").types;

const app = express();
app.use(cors());
app.use(express.json());
app.use(express.static('public'));

// Configuração do Ambiente. Deve bater na API do Laravel exposta no Docker
const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://localhost:8084/api/tickets/generate';
const PRINTER_NAME = process.env.PRINTER_NAME || 'Comum_Impressora_Ficticia'; 
// OBS: Em Produção no Windows, instalar a impressora e passar o nome da fila dela acima. Substituir por ex: '\\\\localhost\\EPSON_TM_T20'
const IS_PRODUCTION = false; // Mude para true para tentar imprimir nativamente no C++ bindings do windows

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
    if (!IS_PRODUCTION) {
        console.log(`[DEV MODE] Imprimindo MOCK no Totem: Senha ${ticketStr} (${typeStr})`);
        return;
    }

    let printer = new ThermalPrinter({
        type: PrinterTypes.EPSON,
        interface: 'printer:' + PRINTER_NAME, 
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
