<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_authentication_errors_are_returned_in_portuguese(): void
    {
        $this
            ->getJson('/api/user')
            ->assertUnauthorized()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Autenticação necessária.')
            ->assertJsonPath('data', null);
    }

    public function test_api_authorization_errors_are_returned_in_portuguese(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $template = Template::factory()->for($otherUser)->create();

        $this
            ->actingAs($user)
            ->getJson("/api/templates/{$template->id}")
            ->assertForbidden()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Você não tem permissão para acessar este recurso.')
            ->assertJsonPath('data', null);
    }

    public function test_api_model_not_found_errors_do_not_expose_internal_details(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/templates/999999');

        $response
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Recurso não encontrado.')
            ->assertJsonPath('data', null);

        $this->assertStringNotContainsString('App\\Models', $response->getContent());
        $this->assertStringNotContainsString('No query results', $response->getContent());
    }

    public function test_api_route_not_found_errors_are_returned_in_portuguese(): void
    {
        $this
            ->getJson('/api/route-that-does-not-exist')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Recurso não encontrado.')
            ->assertJsonPath('data', null);
    }

    public function test_api_method_not_allowed_errors_are_returned_in_portuguese(): void
    {
        $this
            ->getJson('/api/auth/login')
            ->assertMethodNotAllowed()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Método HTTP não permitido para este recurso.')
            ->assertJsonPath('data', null);
    }

    public function test_api_validation_errors_are_returned_in_portuguese(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Os dados enviados são inválidos.')
            ->assertJsonPath('data', null)
            ->assertJsonValidationErrors(['email', 'password']);

        $this->assertStringContainsString('obrigatório', $response->json('errors.email.0'));
        $this->assertStringContainsString('senha', $response->json('errors.password.0'));
    }

    public function test_login_rate_limit_errors_are_returned_in_portuguese(): void
    {
        $payload = [
            'email' => 'rate-limit@example.test',
            'password' => 'password123',
        ];

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->postJson('/api/auth/login', $payload);
        }

        $this
            ->postJson('/api/auth/login', $payload)
            ->assertTooManyRequests()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Muitas tentativas. Aguarde um pouco antes de tentar novamente.')
            ->assertJsonPath('data', null)
            ->assertJsonStructure(['success', 'message', 'data', 'retry_after_seconds']);
    }

    public function test_domain_validation_errors_are_returned_in_portuguese(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();

        $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $template->id])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Os dados enviados são inválidos.')
            ->assertJsonPath('data', null)
            ->assertJsonPath('errors.template_id.0', 'O template selecionado deve ter pelo menos um setor.');
    }
}
