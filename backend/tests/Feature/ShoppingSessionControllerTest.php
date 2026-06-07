<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sector;
use App\Models\ShoppingSession;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ShoppingSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_shopping_session_with_template_snapshot(): void
    {
        Carbon::setTestNow('2026-06-07 10:00:00');

        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);

        $secondSector = Sector::factory()->for($template)->create([
            'name' => 'Checkout',
            'order' => 2,
        ]);
        $firstSector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);

        Product::factory()->for($firstSector)->create([
            'name' => 'Banana',
            'created_at' => now()->subMinute(),
        ]);
        Product::factory()->for($firstSector)->create([
            'name' => 'Apple',
            'created_at' => now(),
        ]);
        Product::factory()->for($secondSector)->create(['name' => 'Chocolate']);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $template->id]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.template_id', $template->id)
            ->assertJsonPath('data.expires_at', now()->addDay()->toJSON())
            ->assertJsonPath('data.snapshot.sectors.0.name', 'Produce')
            ->assertJsonPath('data.snapshot.sectors.0.products.0.name', 'Banana')
            ->assertJsonPath('data.snapshot.sectors.0.products.1.name', 'Apple')
            ->assertJsonPath('data.snapshot.sectors.1.name', 'Checkout')
            ->assertJsonPath('data.snapshot.sectors.1.products.0.name', 'Chocolate');
    }

    public function test_starting_session_returns_existing_unexpired_active_session_for_user(): void
    {
        Carbon::setTestNow('2026-06-07 10:00:00');

        $user = User::factory()->create();
        $originalTemplate = Template::factory()->for($user)->create(['name' => 'Original Market']);
        $otherTemplate = Template::factory()->for($user)->create(['name' => 'Other Market']);
        Sector::factory()->for($otherTemplate)->create(['order' => 1]);

        $existingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'template_id' => $originalTemplate->id,
            'status' => 'active',
            'snapshot' => [
                'name' => 'Original Market',
                'sectors' => [
                    ['id' => 123, 'name' => 'Frozen', 'order' => 1, 'products' => []],
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $otherTemplate->id]);

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $existingSession->id)
            ->assertJsonPath('data.template_id', $originalTemplate->id)
            ->assertJsonPath('data.snapshot.sectors.0.name', 'Frozen');

        $this->assertDatabaseCount('shopping_sessions', 1);
    }

    public function test_starting_session_ignores_expired_active_session(): void
    {
        Carbon::setTestNow('2026-06-07 10:00:00');

        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        Sector::factory()->for($template)->create(['name' => 'Produce', 'order' => 1]);

        $expiredSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => 'active',
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $template->id]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.template_id', $template->id);

        $this->assertNotSame($expiredSession->id, $response->json('data.id'));
        $this->assertDatabaseCount('shopping_sessions', 2);
    }

    public function test_current_session_returns_no_content_when_user_has_no_unexpired_active_session(): void
    {
        $user = User::factory()->create();

        ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->subMinute(),
        ]);

        $this
            ->actingAs($user)
            ->getJson('/api/shopping-sessions/current')
            ->assertNoContent();
    }

    public function test_current_session_returns_users_unexpired_active_session(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $template = Template::factory()->for($user)->create();

        ShoppingSession::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'active',
            'snapshot' => ['name' => 'Other Market', 'sectors' => []],
            'expires_at' => now()->addHour(),
        ]);
        $activeSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => 'active',
            'snapshot' => [
                'name' => 'Central Market',
                'sectors' => [
                    ['id' => 10, 'name' => 'Produce', 'order' => 1, 'products' => []],
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/shopping-sessions/current');

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $activeSession->id)
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.template_id', $template->id)
            ->assertJsonPath('data.snapshot.sectors.0.name', 'Produce');
    }

    public function test_shopping_session_routes_require_authentication(): void
    {
        $this
            ->postJson('/api/shopping-sessions', ['template_id' => 1])
            ->assertUnauthorized();

        $this
            ->getJson('/api/shopping-sessions/current')
            ->assertUnauthorized();
    }

    public function test_user_cannot_start_session_from_another_users_template(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $template = Template::factory()->for($otherUser)->create();
        Sector::factory()->for($template)->create(['order' => 1]);

        $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $template->id])
            ->assertForbidden();

        $this->assertDatabaseCount('shopping_sessions', 0);
    }

    public function test_user_cannot_start_session_from_template_without_sectors(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $template->id]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['template_id']);

        $this->assertDatabaseCount('shopping_sessions', 0);
    }

    public function test_session_snapshot_is_not_changed_by_later_template_sector_or_product_edits(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);
        $product = Product::factory()->for($sector)->create(['name' => 'Banana']);

        $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $template->id])
            ->assertCreated();

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}", ['name' => 'Renamed Market'])
            ->assertOk();
        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}", ['name' => 'Fresh Produce'])
            ->assertOk();
        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}", ['name' => 'Green Banana'])
            ->assertOk();
        $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/sectors/{$sector->id}/products", ['name' => 'Apple'])
            ->assertCreated();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/shopping-sessions/current');

        $response
            ->assertOk()
            ->assertJsonPath('data.snapshot.name', 'Central Market')
            ->assertJsonPath('data.snapshot.sectors.0.name', 'Produce')
            ->assertJsonPath('data.snapshot.sectors.0.products.0.name', 'Banana');

        $this->assertSame(
            ['Banana'],
            array_column($response->json('data.snapshot.sectors.0.products'), 'name')
        );
    }
}
