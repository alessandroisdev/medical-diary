<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceCallController extends Controller
{
    /**
     * Exibe o telão de chamadas (Painel).
     */
    public function panel()
    {
        return view('attendance.panel');
    }

    /**
     * Rota de Trigger para chamar um paciente
     * Reberá requisição via AJAX POST.
     */
    public function callPatient(Request $request, Appointment $appointment)
    {
        $appointment->update(['status' => 'in_consultation']);

        // Persiste a chamada no Redis (PubSub simulado) ou cache para o SSE pegar
        $callData = [
            'id' => $appointment->id,
            'client_name' => $appointment->client->name,
            'doctor_name' => $appointment->doctor->name,
            'room' => $request->input('room', 'Consultório Base'),
            'type' => $appointment->consultation_type,
            'time' => now()->format('H:i')
        ];

        // Vamos usar o Cache do Redis para armazenar a chamada atual por alguns minutos
        \Cache::put('current_attendance_call', json_encode($callData), now()->addMinutes(10));
        
        // Em um sistema multi-salas, seria interessante um broadcast list. Aqui simplificamos pra tela.
        return response()->json(['message' => 'Paciente chamado com sucesso!']);
    }

    /**
     * Stream Server-Sent Events (SSE)
     */
    public function stream()
    {
        // Headers necessários para SSE e para evitar cache em proxy/nginx
        $response = new StreamedResponse(function () {
            // Tira restrição de tempo do script
            set_time_limit(0);
            
            // Impede buffer de saída do PHP atrapalhar o real-time
            if (ob_get_level() > 0) ob_end_flush();
            flush();

            // Pega uma referência "vazia" inicialmente para n\ao estourar updates
            $lastCallHash = md5('');

            // Fica num loop aguardando atualizações
            // Nginx precisa estar configurado para proxy_buffering off;
            while (true) {
                // Checa no cache rápido se houve chamada
                $currentCall = \Cache::get('current_attendance_call');
                
                if ($currentCall) {
                    $currentHash = md5($currentCall);
                    
                    if ($currentHash !== $lastCallHash) {
                        $lastCallHash = $currentHash;
                        
                        // Formato obrigatório estruturado de SSE: 
                        // event: NomeDoEvento\n
                        // data: JsonDaData\n\n
                        echo "event: NewCall\n";
                        echo "data: {$currentCall}\n\n";
                        
                        // Garante o envio imediato
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
                
                // Ping para manter conexão viva a cada 10 seg (evita timeout Nginx)
                echo ": ping\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();

                // Dorme 1 segundo para não sobrecarregar processamento (em prod usa-se pub/sub redis direto ao inves de loop php long-running quando possível)
                sleep(2);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        // Prevenir proxy de Nginx de fazer buffering se não setado em default.conf
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}
