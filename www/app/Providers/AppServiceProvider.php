<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Força idioma
        \Carbon\Carbon::setLocale('pt_BR');

        // Configura SMTP Dinamicamente via Settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $host = \App\Models\Setting::get('smtp_host');
                if ($host) {
                    config([
                        'mail.mailers.smtp.host' => $host,
                        'mail.mailers.smtp.port' => \App\Models\Setting::get('smtp_port', 587),
                        'mail.mailers.smtp.encryption' => \App\Models\Setting::get('smtp_encryption', 'tls'),
                        'mail.mailers.smtp.username' => \App\Models\Setting::get('smtp_username'),
                        'mail.mailers.smtp.password' => \App\Models\Setting::get('smtp_password'),
                        'mail.from.address' => \App\Models\Setting::get('contact_email', 'noreply@medical.diary'),
                        'mail.from.name' => 'Medical Diary System',
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignorado em fase de setup inicial da base
        }
    }
}
