<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_login_returns_token_and_user_data(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure([
                    'token',
                    'user' => ['id', 'name', 'email'],
                ]);

        $this->assertNotEmpty($response->json('token'));
    }

    #[Test]
    public function test_login_validation_errors(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors(['email', 'password']);
    }

    #[Test]
    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'incorrect',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
                ->assertJson([
                    'message' => 'Les identifiants sont incorrects.',
                ]);
}

    #[Test]
    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function test_authenticated_user_can_access_me(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/me');

        $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                    ],
                ]);
    }

    #[Test]
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'message' => 'Déconnexion réussie.',
                ]);
    }

    #[Test]
    public function test_logout_removes_access_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/logout');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

}
