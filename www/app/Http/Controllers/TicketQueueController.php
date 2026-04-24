<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ServiceTicket;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TicketQueueController extends Controller
{
    // -------------------------------------------------------------
    // API PUBLICA PARA O TOTEM (Node.JS via USB Printer)
    // -------------------------------------------------------------
    public function generate(Request $request)
    {
        $request->validate(['type' => 'required|in:priority,common']);
        
        $type = $request->type;
        $prefix = $type === 'priority' ? 'P' : 'C';

        // Pega quantos tiveram no dia
        $countToday = ServiceTicket::whereDate('created_at', Carbon::today())
                                   ->where('type', $type)
                                   ->count();
        
        $nextNumber = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        $ticketValue = $prefix . $nextNumber;

        $ticket = ServiceTicket::create([
            'type' => $type,
            'number' => $ticketValue,
            'status' => 'waiting'
        ]);

        return response()->json([
            'message' => 'Senha gerada com sucesso',
            'ticket' => $ticketValue,
            'id' => $ticket->id
        ]);
    }

    // -------------------------------------------------------------
    // ÁREA RESTRITA PARA RECEPÇÃO (Web Route)
    // -------------------------------------------------------------
    
    // View do Painel da Recepção (Fila)
    public function index()
    {
        $waitingTickets = ServiceTicket::whereDate('created_at', Carbon::today())
                                       ->where('status', 'waiting')
                                       ->orderBy('created_at', 'asc')
                                       ->get();

        $activeTicket = ServiceTicket::where('collaborator_id', auth()->guard('collaborator')->id())
                                     ->where('status', 'calling')
                                     ->first();

        return view('reception.queue', compact('waitingTickets', 'activeTicket'));
    }

    // Lógica para chamar o próximo via botão "Chamar Próximo"
    public function callNext()
    {
        $collaborator = auth()->guard('collaborator')->user();

        if (!$collaborator->current_room) {
            return response()->json(['error' => 'Você precisa fazer o Check-in em um Guichê primeiro!'], 403);
        }

        // Descobre pesos configurados (Defalts P:2 / C:1)
        $pWeight = Setting::where('key', 'ticket_ratio_priority')->value('value') ?? 2;
        $cWeight = Setting::where('key', 'ticket_ratio_common')->value('value') ?? 1;

        // Pega o histórico global recente das chamadas de hoje para saber quem foi o ultimo
        $lastCalls = ServiceTicket::whereDate('called_at', Carbon::today())
                                  ->whereNotNull('called_at')
                                  ->orderBy('called_at', 'desc')
                                  ->take((int)$pWeight + (int)$cWeight)
                                  ->pluck('type');

        // Conta quantos foram recentemente
        $countP = $lastCalls->filter(fn($t) => $t == 'priority')->count();
        // $countC = $lastCalls->filter(fn($t) => $t == 'common')->count();

        $typeToCall = 'common';
        
        // Verifica se existem pessoas na priority esperando
        $hasPriorityWaiting = ServiceTicket::whereDate('created_at', Carbon::today())->where('type', 'priority')->where('status', 'waiting')->exists();
        $hasCommonWaiting = ServiceTicket::whereDate('created_at', Carbon::today())->where('type', 'common')->where('status', 'waiting')->exists();

        if ($hasPriorityWaiting && (!$hasCommonWaiting || $countP < $pWeight)) {
            $typeToCall = 'priority';
        } else if (!$hasCommonWaiting && $hasPriorityWaiting) {
             $typeToCall = 'priority';
        }

        // Pega o primeiro dessa fila escolhida
        $nextTicket = ServiceTicket::whereDate('created_at', Carbon::today())
                                   ->where('type', $typeToCall)
                                   ->where('status', 'waiting')
                                   ->orderBy('created_at', 'asc')
                                   ->first();

        // Fallback: Se não achou na que escolheu, tenta a outra
        if (!$nextTicket) {
             $typeToCall = $typeToCall === 'priority' ? 'common' : 'priority';
             $nextTicket = ServiceTicket::whereDate('created_at', Carbon::today())
                                   ->where('type', $typeToCall)
                                   ->where('status', 'waiting')
                                   ->orderBy('created_at', 'asc')
                                   ->first();
        }

        if (!$nextTicket) {
            return response()->json(['error' => 'Fila vazia. Não há mais pacientes aguardando senha!'], 404);
        }

        // Se tem alguem agarrado Calling com o Colaborador, cancela/ignora
        ServiceTicket::where('collaborator_id', $collaborator->id)
                     ->where('status', 'calling')
                     ->update(['status' => 'attended', 'comment' => 'Finalizado ao chamar o próximo (Auto)']);

        // Atualiza ticket
        $nextTicket->update([
            'status' => 'calling',
            'collaborator_id' => $collaborator->id,
            'called_at' => Carbon::now()
        ]);

        // Redis patch/SSE TV trigger (Reaproveita do AttendanceCallController)
        $callData = [
            'id' => $nextTicket->id, // Usa uuid mesmo
            'client_name' => 'Senha ' . $nextTicket->number,
            'doctor_name' => 'Recepcionista',
            'room' => 'Guichê ' . $collaborator->current_room, // Ex Guichê 1
            'is_ticket' => true,
            'ticket_type' => $nextTicket->type, // Vai ajudar o CSS no painel
            'time' => now()->format('H:i')
        ];
        \Cache::put('current_attendance_call', json_encode($callData), now()->addMinutes(10));

        return response()->json([
            'message' => 'Senha Chamada com Sucesso!', 
            'ticket' => $nextTicket->number,
            'room' => $collaborator->current_room
        ]);
    }

    public function finish(Request $request, ServiceTicket $ticket)
    {
        $ticket->update([
            'status' => 'attended',
            'attended_at' => Carbon::now(),
            'comment' => $request->input('comment')
        ]);

        return response()->json(['message' => 'Atendimento finalizado no sistema!']);
    }
}
