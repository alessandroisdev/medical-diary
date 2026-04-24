<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReceived;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:25',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        $message = ContactMessage::create($validated);

        // Dispara o email enfileirado
        try {
            $destEmail = Setting::get('contact_email');
            if($destEmail) {
                Mail::to($destEmail)->queue(new ContactReceived($message));
            }
        } catch (\Exception $e) {
            Log::error('Erro ao colocar e-mail na fila: ' . $e->getMessage());
            // Não quebra a experiência do paciente
        }

        return response()->json([
            'success' => true,
            'message' => 'Sua mensagem foi entregue com sucesso! Aguarde nosso contato.'
        ]);
    }
}
