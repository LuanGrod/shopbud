<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sector;
use App\Models\ShoppingSession;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $expiredSession->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_starting_session_does_not_reactivate_cancelled_session(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        Sector::factory()->for($template)->create(['name' => 'Produce', 'order' => 1]);

        $cancelledSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => 'cancelled',
            'expires_at' => now()->addHour(),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/shopping-sessions', ['template_id' => $template->id]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.template_id', $template->id);

        $this->assertNotSame($cancelledSession->id, $response->json('data.id'));
        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $cancelledSession->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_current_session_returns_no_content_when_user_has_no_unexpired_active_session(): void
    {
        $user = User::factory()->create();

        $expiredSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->subMinute(),
        ]);

        $this
            ->actingAs($user)
            ->getJson('/api/shopping-sessions/current')
            ->assertNoContent();

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $expiredSession->id,
            'status' => 'cancelled',
        ]);
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

    public function test_user_can_cancel_own_active_shopping_session(): void
    {
        $user = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => now()->addHour(),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/cancel");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $shoppingSession->id)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_user_can_finish_active_session_with_official_totals_calculated_by_backend(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        $produce = Sector::factory()->for($template)->create(['name' => 'Produce', 'order' => 1]);
        $bakery = Sector::factory()->for($template)->create(['name' => 'Bakery', 'order' => 2]);
        Product::factory()->for($produce)->create(['name' => 'Banana']);

        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => 'active',
            'snapshot' => [
                'name' => 'Central Market',
                'sectors' => [
                    ['id' => $produce->id, 'name' => 'Produce', 'order' => 1, 'products' => [['id' => 10, 'name' => 'Banana']]],
                    ['id' => $bakery->id, 'name' => 'Bakery', 'order' => 2, 'products' => []],
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $this->assertDatabaseCount('shopping_items', 0);

        $response = $this
            ->actingAs($user)
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/finish", [
                'total' => 9999,
                'items' => [
                    [
                        'sector_snapshot_id' => $produce->id,
                        'product_name' => 'Banana',
                        'price' => 2.50,
                        'quantity' => 3,
                        'extra' => false,
                    ],
                    [
                        'sector_snapshot_id' => $bakery->id,
                        'product_name' => 'Cake',
                        'price' => 11.25,
                        'quantity' => 2,
                        'extra' => true,
                    ],
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'finished')
            ->assertJsonPath('data.summary.total', '30.00')
            ->assertJsonPath('data.summary.sectors.0.subtotal', '7.50')
            ->assertJsonPath('data.summary.sectors.0.items.0.product_name', 'Banana')
            ->assertJsonPath('data.summary.sectors.1.subtotal', '22.50')
            ->assertJsonPath('data.summary.sectors.1.items.0.product_name', 'Cake')
            ->assertJsonPath('data.summary.sectors.1.items.0.extra', true);

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'finished',
        ]);
        $this->assertDatabaseHas('shopping_items', [
            'session_id' => $shoppingSession->id,
            'sector_name' => 'Produce',
            'product_name' => 'Banana',
            'price' => 2.50,
            'quantity' => 3,
            'extra' => false,
        ]);
        $this->assertDatabaseHas('shopping_items', [
            'session_id' => $shoppingSession->id,
            'sector_name' => 'Bakery',
            'product_name' => 'Cake',
            'price' => 11.25,
            'quantity' => 2,
            'extra' => true,
        ]);
        $this->assertDatabaseHas('purchase_histories', [
            'user_id' => $user->id,
            'template_name' => 'Central Market',
            'total' => 30.00,
        ]);
        $this->assertDatabaseHas('templates', [
            'id' => $template->id,
            'name' => 'Central Market',
        ]);
        $this->assertDatabaseHas('sectors', [
            'id' => $produce->id,
            'name' => 'Produce',
        ]);
        $this->assertDatabaseHas('products', [
            'sector_id' => $produce->id,
            'name' => 'Banana',
        ]);
        $this->assertDatabaseMissing('products', [
            'sector_id' => $bakery->id,
            'name' => 'Cake',
        ]);
    }

    public function test_user_can_finish_active_session_with_empty_payload(): void
    {
        $user = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
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
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/finish", []);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'finished')
            ->assertJsonPath('data.summary.total', '0.00')
            ->assertJsonPath('data.summary.sectors.0.subtotal', '0.00')
            ->assertJsonCount(0, 'data.summary.sectors.0.items');

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'finished',
        ]);
        $this->assertDatabaseCount('shopping_items', 0);
        $this->assertDatabaseHas('purchase_histories', [
            'user_id' => $user->id,
            'template_name' => 'Central Market',
            'total' => 0,
        ]);
    }

    public function test_finish_validates_minimum_price_and_quantity_for_items(): void
    {
        $user = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
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
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/finish", [
                'items' => [
                    [
                        'sector_snapshot_id' => 10,
                        'product_name' => 'Banana',
                        'price' => 0,
                        'quantity' => 0,
                        'extra' => false,
                    ],
                ],
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'items.0.price',
                'items.0.quantity',
            ]);

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseCount('shopping_items', 0);
    }

    public function test_finish_rejects_item_outside_the_session_snapshot(): void
    {
        $user = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
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
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/finish", [
                'items' => [
                    [
                        'sector_snapshot_id' => 999,
                        'product_name' => 'Banana',
                        'price' => 1.99,
                        'quantity' => 1,
                        'extra' => false,
                    ],
                ],
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.sector_snapshot_id']);

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseCount('shopping_items', 0);
    }

    public function test_user_cannot_finish_another_users_shopping_session(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'active',
            'snapshot' => [
                'name' => 'Central Market',
                'sectors' => [
                    ['id' => 10, 'name' => 'Produce', 'order' => 1, 'products' => []],
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $this
            ->actingAs($user)
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/finish", ['items' => []])
            ->assertForbidden();

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseCount('shopping_items', 0);
    }

    public function test_user_cannot_cancel_another_users_shopping_session(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'active',
            'expires_at' => now()->addHour(),
        ]);

        $this
            ->actingAs($user)
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/cancel")
            ->assertForbidden();

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'active',
        ]);
    }

    /**
     * @return array<string, array{status: string}>
     */
    public static function nonActiveShoppingSessionStatuses(): array
    {
        return [
            'finished' => ['status' => 'finished'],
            'cancelled' => ['status' => 'cancelled'],
        ];
    }

    #[DataProvider('nonActiveShoppingSessionStatuses')]
    public function test_user_cannot_cancel_non_active_shopping_session(string $status): void
    {
        $user = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'status' => $status,
            'expires_at' => now()->addHour(),
        ]);

        $this
            ->actingAs($user)
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/cancel")
            ->assertForbidden();

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => $status,
        ]);
    }

    #[DataProvider('nonActiveShoppingSessionStatuses')]
    public function test_user_cannot_finish_non_active_shopping_session(string $status): void
    {
        $user = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'status' => $status,
            'snapshot' => [
                'name' => 'Central Market',
                'sectors' => [
                    ['id' => 10, 'name' => 'Produce', 'order' => 1, 'products' => []],
                ],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $this
            ->actingAs($user)
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/finish", ['items' => []])
            ->assertForbidden();

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => $status,
        ]);
        $this->assertDatabaseCount('shopping_items', 0);
        $this->assertDatabaseCount('purchase_histories', 0);
    }

    public function test_user_cannot_finish_expired_active_shopping_session(): void
    {
        $user = User::factory()->create();
        $shoppingSession = ShoppingSession::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'snapshot' => [
                'name' => 'Central Market',
                'sectors' => [
                    ['id' => 10, 'name' => 'Produce', 'order' => 1, 'products' => []],
                ],
            ],
            'expires_at' => now()->subMinute(),
        ]);

        $this
            ->actingAs($user)
            ->postJson("/api/shopping-sessions/{$shoppingSession->id}/finish", ['items' => []])
            ->assertForbidden();

        $this->assertDatabaseHas('shopping_sessions', [
            'id' => $shoppingSession->id,
            'status' => 'cancelled',
        ]);
        $this->assertDatabaseCount('shopping_items', 0);
        $this->assertDatabaseCount('purchase_histories', 0);
    }

    public function test_shopping_session_routes_require_authentication(): void
    {
        $this
            ->postJson('/api/shopping-sessions', ['template_id' => 1])
            ->assertUnauthorized();

        $this
            ->getJson('/api/shopping-sessions/current')
            ->assertUnauthorized();

        $this
            ->postJson('/api/shopping-sessions/1/cancel')
            ->assertUnauthorized();

        $this
            ->postJson('/api/shopping-sessions/1/finish')
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
