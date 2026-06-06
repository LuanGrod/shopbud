<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // ==================== REGISTER ====================

    public function test_user_can_register_with_valid_data(): void
    {
        // Arrange
        $data = [
            "name" => "User Test",
            "email" => "user@email.com",
            "password" => "password123",
            "password_confirmation" => "password123"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['success', 'user', 'token'])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'name' => 'User Test',
            'email' => 'user@email.com'
        ]);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        // Arrange
        User::factory()->create(['email' => 'existing@email.com']);

        $data = [
            "name" => "User Test",
            "email" => "existing@email.com",
            "password" => "password123",
            "password_confirmation" => "password123"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_without_name(): void
    {
        // Arrange
        $data = [
            "email" => "user@email.com",
            "password" => "password123",
            "password_confirmation" => "password123"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_register_fails_without_email(): void
    {
        // Arrange
        $data = [
            "name" => "User Test",
            "password" => "password123",
            "password_confirmation" => "password123"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_invalid_email(): void
    {
        // Arrange
        $data = [
            "name" => "User Test",
            "email" => "invalid-email",
            "password" => "password123",
            "password_confirmation" => "password123"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_without_password(): void
    {
        // Arrange
        $data = [
            "name" => "User Test",
            "email" => "user@email.com"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_fails_when_password_confirmation_does_not_match(): void
    {
        // Arrange
        $data = [
            "name" => "User Test",
            "email" => "user@email.com",
            "password" => "password123",
            "password_confirmation" => "different123"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_fails_with_short_password(): void
    {
        // Arrange
        $data = [
            "name" => "User Test",
            "email" => "user@email.com",
            "password" => "short",
            "password_confirmation" => "short"
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // ==================== LOGIN ====================

    public function test_user_can_login_with_valid_credentials(): void
    {
        // Arrange
        $password = "password123";
        User::factory()->create([
            "email" => "user@email.com",
            "password" => $password
        ]);

        $data = [
            "email" => "user@email.com",
            "password" => $password
        ];

        // Act
        $response = $this->postJson("/api/auth/login", $data);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'token'])
            ->assertJson(['success' => true]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        // Arrange
        User::factory()->create([
            "email" => "user@email.com",
            "password" => "password123"
        ]);

        $data = [
            "email" => "user@email.com",
            "password" => "wrongpassword"
        ];

        // Act
        $response = $this->postJson("/api/auth/login", $data);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        // Arrange
        $data = [
            "email" => "nonexistent@email.com",
            "password" => "password123"
        ];

        // Act
        $response = $this->postJson("/api/auth/login", $data);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_without_email(): void
    {
        // Arrange
        $data = [
            "password" => "password123"
        ];

        // Act
        $response = $this->postJson("/api/auth/login", $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_without_password(): void
    {
        // Arrange
        $data = [
            "email" => "user@email.com"
        ];

        // Act
        $response = $this->postJson("/api/auth/login", $data);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // ==================== USER ====================

    public function test_authenticated_user_can_view_current_user(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'User Test',
            'email' => 'user@email.com',
        ]);
        $token = $user->createToken('test')->plainTextToken;

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        // Assert
        $response
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', 'User Test')
            ->assertJsonPath('email', 'user@email.com')
            ->assertJsonMissingPath('password');
    }

    public function test_user_endpoint_fails_without_token(): void
    {
        // Act
        $response = $this->getJson('/api/user');

        // Assert
        $response
            ->assertUnauthorized();
    }

    // ==================== LOGOUT ====================

    public function test_user_can_logout_with_valid_token(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_revokes_current_token_for_protected_routes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Act
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->refreshApplication();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        // Assert
        $response
            ->assertUnauthorized();
    }

    public function test_logout_fails_without_token(): void
    {
        // Act
        $response = $this->postJson('/api/auth/logout');

        // Assert
        $response
            ->assertUnauthorized();
    }

    public function test_logout_fails_with_invalid_token(): void
    {
        // Act
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->postJson('/api/auth/logout');

        // Assert
        $response
            ->assertUnauthorized();
    }
}
