<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppMetaChannel
{
    /**
     * Envia a notificação oficial pelo WhatsApp.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Puxa o método formatador da notificação
        if (! method_exists($notification, 'toWhatsAppMeta')) {
            return;
        }

        $message = $notification->toWhatsAppMeta($notifiable);
        $phone = $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone;

        // Limpeza básica do telefone
        $phone = preg_replace('/\D/', '', $phone);
        // Garante DDI 55 (Brasil) caso não tenha
        if (strlen($phone) <= 11) {
            $phone = '55' . $phone;
        }

        $token = env('WHATSAPP_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_ID');
        
        if (! $token || ! $phoneId) {
            Log::warning('WhatsAppMetaChannel: Credenciais do Meta não configuradas no .env');
            return;
        }

        $url = "https://graph.facebook.com/v19.0/{$phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'appointment_reminder',
                'language' => [
                    'code' => 'pt_BR'
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $message['patient_name'] ?? 'Paciente'
                            ],
                            [
                                'type' => 'text',
                                'text' => $message['date'] ?? 'sua próxima consulta'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withToken($token)
                ->post($url, $payload);

            if ($response->failed()) {
                Log::error('WhatsAppMetaChannel Falha: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('WhatsAppMetaChannel Erro Fatal: ' . $e->getMessage());
        }
    }
}
