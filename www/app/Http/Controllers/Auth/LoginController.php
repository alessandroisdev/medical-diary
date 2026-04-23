<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *      version="10.0.0",
 *      title="Medical Diary SaaS API",
 *      description="API Central do sistema administrativo.",
 *      @OA\Contact(
 *          email="admin@medical.diary"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8084",
 *      description="Local API Server"
 * )
 */
class LoginController extends Controller
{
    /**
     * Exibe o formulário único com multi-abas para o login.
     *
     * @OA\Get(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Display multi-guard login interface",
     *     @OA\Response(response=200, description="Login page rendered")
     * )
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa a autenticação dinamicamente baseado na origem (guard)
     *
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Authenticate user across different guards",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="guard", type="string", enum={"admin", "doctor", "collaborator", "client"})
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Authenticated via Ajax"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'guard' => 'required|in:admin,doctor,collaborator,client'
        ]);

        $credentials = $request->only('email', 'password');
        $guard = $request->input('guard');

        if (Auth::guard($guard)->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Define o painel de destino baseado no perfil
            $intents = [
                'client' => '/portal',
                'doctor' => '/records',
                'collaborator' => '/appointments',
                'admin' => '/transactions'
            ];

            return response()->json([
                'message' => 'Login aprovado! Redirecionando...',
                'redirect' => $intents[$guard] ?? '/appointments'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Credenciais incorretas ou conta inativa.'
        ], 401);
    }

    /**
     * Finaliza a sessão independentemente do guard
     */
    public function logout(Request $request)
    {
        foreach (['admin', 'doctor', 'collaborator', 'client'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
