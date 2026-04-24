<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpDeskController extends Controller
{
    public function index()
    {
        // Redirecionamento dinâmico pro guard correto (pra quem chamou a Rota Genérica /help)
        if (auth()->guard('admin')->check()) return redirect()->route('help.admin');
        if (auth()->guard('doctor')->check()) return redirect()->route('help.doctor');
        if (auth()->guard('collaborator')->check()) return redirect()->route('help.collaborator');
        if (auth()->guard('client')->check()) return redirect()->route('help.client');

        return redirect('/login');
    }

    public function admin()
    {
        return view('help.admin');
    }

    public function doctor()
    {
        return view('help.doctor');
    }

    public function collaborator()
    {
        return view('help.collaborator');
    }

    public function client()
    {
        return view('help.client');
    }
}
