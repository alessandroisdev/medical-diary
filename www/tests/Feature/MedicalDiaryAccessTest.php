<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalDiaryAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Um paciente não logado que tentar acessar o painel restrito
     * deve ser barrado com 302 ou 401 unauthenticated exception.
     */
    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/portal');
        
        $response->assertStatus(302);
        // O core do Laravel joga o guest pro route('login')
        $response->assertRedirect('/login');
    }

    /**
     * Teste: Paciente devidamente logado acessa apenas o portal, mas
     * toma Blocked (403/Redirect vazio/Redirect Home) caso tente invadir o Financeiro
     */
    public function test_client_cannot_access_financial_dashboard()
    {
        // Forja um paciente
        $client = Client::create([
            'name' => 'Teste',
            'email' => 'teste@teste.com',
            'cpf' => '000.000.000-00',
            'password' => \Hash::make('123')
        ]);

        // Autentica via Helper ActingAs apontando pro Guard correto
        $this->actingAs($client, 'client');

        // Acesso ao portal deve funcionar
        $responsePortal = $this->get('/portal');
        $responsePortal->assertStatus(200);

        // Tentativa de invasão ao financeiro
        $responseFinance = $this->get('/transactions');
        
        // Laravel devolve 403 HTTP Access Denied caso o middleware falhe o role base auth check config,
        // mas as vezes devolve 401/302 se o auth estritamente falhar, ou Forbidden action. 
        // Com auth:admin, caso o user seja guard diferente, ele lança AuthenticationException (302) redirect para login ou home
        $responseFinance->assertStatus(302); 
    }

    /**
     * Teste: Admnistrador master acessando a área financeira
     */
    public function test_admin_can_access_transactions()
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->get('/transactions');
        $response->assertStatus(200);
    }
}
