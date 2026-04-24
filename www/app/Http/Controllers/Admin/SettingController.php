<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        
        $groupedSettings = [
            'Logística e Site' => $settings->filter(fn($s) => str_contains($s->label, '[Logística]') || str_contains($s->label, '[Website]')),
            'Tráfego e Contato' => $settings->filter(fn($s) => str_contains($s->label, '[Contato]') || str_contains($s->label, '[Redes Sociais]')),
            'Página: Home' => $settings->filter(fn($s) => str_contains($s->label, '[Website Home]')),
            'Página: Infraestrutura' => $settings->filter(fn($s) => str_contains($s->label, '[Website Infra]')),
            'Página: Outras' => $settings->filter(fn($s) => str_contains($s->label, '[Website Especialidades]') || str_contains($s->label, '[Website Equipe]')),
            'Servidor SMTP' => $settings->filter(fn($s) => str_contains($s->label, '[SMTP Servidor]')),
            'Sem Categoria' => $settings->filter(fn($s) => !str_contains($s->label, '[')),
        ];

        return view('admin.settings.index', compact('groupedSettings'));
    }

    public function update(Request $request)
    {
        $payload = $request->except(['_token', '_method']);

        foreach ($payload as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->back()->with('success', 'Configurações Globais atualizadas com sucesso!');
    }
}
