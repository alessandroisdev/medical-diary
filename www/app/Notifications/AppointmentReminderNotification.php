<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Appointment;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    public $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Define the delivery channels dynamically.
     * Supports multi-channel based on user preferences.
     */
    public function via(object $notifiable): array
    {
        // Aqui o SAAS decidiria buscar a pref do notifiable (database, mail, sms, telegram...)
        // Retornamos estaticamente as opções que o engine base pode processar
        return ['mail', 'database', \App\Channels\WhatsAppMetaChannel::class]; 
        
        // Exemplo futuro plugado:
        // return ['mail', \NotificationChannels\Twilio\TwilioChannel::class, \NotificationChannels\Telegram\TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Lembrete de Consulta - Medical Diary')
                    ->greeting('Olá, ' . $this->appointment->client->name)
                    ->line('Sua consulta com Dr(a). ' . $this->appointment->doctor->name . ' está confirmada.')
                    ->line('Por favor, compareça no dia: ' . $this->appointment->scheduled_at->format('d/m/Y \à\s H:i'))
                    ->action('Ver Detalhes do Agendamento', url('/'))
                    ->line('Obrigado por usar nossa plataforma clínica!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'message' => 'Lembrete de consulta agendada para ' . $this->appointment->scheduled_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Example method for SMS via Twilio channel (Pseudo code template)
     */
    public function toTwilio($notifiable)
    {
        /*
        return (new TwilioSmsMessage())
            ->content("Medical Diary: Sua consulta é amanhã às ".$this->appointment->scheduled_at->format('H:i'));
        */
    }

    /**
     * Payload nativo para a WhatsApp Cloud API (Meta)
     */
    public function toWhatsAppMeta($notifiable): array
    {
        return [
            'patient_name' => $this->appointment->client->name,
            'date' => $this->appointment->scheduled_at->format('d/m/Y \à\s H:i')
        ];
    }
}
