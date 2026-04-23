<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Documento Médico - {{ $record->client->name }}</title>
    
    <!-- Chamamos o Bootstrap pela CDN só para a formatação da página isolada do layout principal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body { 
            background: #f8f9fa;
        }
        .a4-sheet {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        /* Regras exclusivas na hora de dar o print na impressora/pdf */
        @media print {
            body, page {
                margin: 0;
                box-shadow: none;
                background: white;
            }
            .a4-sheet {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: 100vh;
            }
            .no-print {
                display: none !important;
            }
        }
        
        .header-doc {
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .clinic-n { font-size: 1.5rem; font-weight: 800; letter-spacing: 1px;}
        .section-title { font-size: 1.1rem; border-bottom: 1px solid #ddd; font-weight: bold; margin-top: 30px; margin-bottom: 15px;}
        .signature-box { border-top: 1px solid #444; width: 60%; margin: 60px auto 20px; text-align: center; padding-top: 5px; }
        .hash-validate { font-family: monospace; text-align: center; font-size: 0.8rem; color: #555; }
    </style>
</head>
<body>

    <!-- Botão que desaparece na hora de imprimir -->
    <div class="text-center mt-3 mb-2 no-print">
        <button class="btn btn-dark px-4 py-2 fw-bold" onclick="window.print()">
            🖨️ CONFIRMAR IMPRESSÃO (PDF)
        </button>
    </div>

    <!-- Papel Documento -->
    <div class="a4-sheet">
        <div class="header-doc d-flex justify-content-between align-items-center">
            <div>
                <div class="clinic-n">MEDICAL DIARY CLINIC</div>
                <div class="text-muted" style="font-size: 0.9rem;">
                    Rua das Clínicas, 123 - Centro<br>
                    CEP: 00000-000 - São Paulo/SP
                </div>
            </div>
            <div class="text-end">
                <div class="fw-bold">Prontuário Emitido Em:</div>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        @if(!isset($isClientView) || !$isClientView)
            <div class="row mb-4 bg-light p-3 border rounded shadow-sm">
                <div class="col-8"><strong>Paciente:</strong> {{ $record->client->name }}</div>
                <div class="col-4"><strong>CPF:</strong> {{ $record->client->cpf ?? 'Não Informado' }}</div>
                <div class="col-12 mt-2"><strong>Médico Assistente:</strong> Dr(a). {{ $record->doctor->name }} | <strong>CRM:</strong> {{ $record->doctor->crm }}</div>
            </div>

            <div class="section-title">REGISTRO CLÍNICO (PRONTUÁRIO)</div>
            
            <p><strong>Sintomatologia e Queixas:</strong></p>
            <div class="border p-2 mb-3 bg-light rounded" style="white-space: pre-line;">
                {{ $record->symptoms ?? 'Nenhum sintoma ou queixa preenchida.' }}
            </div>
            
            <p><strong>Diagnóstico Formal / Resultado:</strong></p>
            <p class="fs-5 text-danger fw-bold">{{ $record->diagnosis }}</p>

            <p><strong>Conduta e Plano de Tratamento:</strong></p>
            <div class="border p-2 bg-light rounded" style="white-space: pre-line;">
                {{ $record->treatment_plan }}
            </div>

            @if($prescription)
                <div style="page-break-before: always;"></div>
            @endif
        @endif

        @if($prescription)
            
            <!-- Reseta cabeçalho da segunda via (Seção de Receituário) se houver -->
            <div class="header-doc d-flex justify-content-between align-items-center mt-4">
                <div class="clinic-n">RECEITUÁRIO MÉDICO</div>
                <div class="text-end">
                    <div class="fw-bold fs-5 text-danger">USO PACIENTE</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12"><strong>Paciente:</strong> {{ $record->client->name }}</div>
            </div>

            <div class="section-title text-success">MEDICAÇÕES PRESCRITAS</div>
            
            @if($prescription->medicines && is_array($prescription->medicines))
                <ul class="list-group list-group-flush border-bottom mb-4">
                    @foreach($prescription->medicines as $med)
                        @if(!empty($med))
                            <li class="list-group-item mx-0 px-0 fs-5 pb-3"> <span class="fw-bold">→</span> {{ $med }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif

            <p><strong>Orientações de Posologia:</strong></p>
            <div class="border p-3 rounded" style="white-space: pre-line; min-height: 150px; font-size:1.1rem">
                {{ $prescription->instructions }}
            </div>

            <div class="signature-box mt-5">
                <strong>Dr(a). {{ $record->doctor->name }}</strong><br>
                CRM: {{ $record->doctor->crm }}<br>
                Assinatura do Responsável
            </div>
            
            <div class="hash-validate mt-3">
                <small>Assinatura Digital Eletrônica. Autenticador Token Hash: <strong>{{ $prescription->signature_hash }}</strong></small><br>
                <small>Válido até: {{ $prescription->valid_until ? $prescription->valid_until->format('d/m/Y') : 'Data Indeterminada' }}</small>
            </div>
        @else
            <!-- Prontuário Sem Receita -->
             <div class="signature-box mt-5">
                <strong>Dr(a). {{ $record->doctor->name }}</strong><br>
                CRM: {{ $record->doctor->crm }}<br>
                Registro Assinado
            </div>
        @endif

    </div>

</body>
</html>
