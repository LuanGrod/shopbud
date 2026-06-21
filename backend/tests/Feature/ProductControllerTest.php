<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sector;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_sector_products_in_creation_order(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);

        $earlierProduct = Product::query()->create([
            'sector_id' => $sector->id,
            'name' => 'Banana',
        ]);
        $laterProduct = Product::query()->create([
            'sector_id' => $sector->id,
            'name' => 'Apple',
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson("/api/templates/{$template->id}/sectors/{$sector->id}/products");

        $response
            ->assertOk()
            ->assertJsonPath('data.0', [
                'id' => $earlierProduct->id,
                'name' => 'Banana',
            ])
            ->assertJsonPath('data.1', [
                'id' => $laterProduct->id,
                'name' => 'Apple',
            ]);
    }

    public function test_user_can_create_product_with_name_unique_within_sector(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);
        $otherSector = Sector::factory()->for($template)->create([
            'name' => 'Bakery',
            'order' => 2,
        ]);

        Product::query()->create([
            'sector_id' => $otherSector->id,
            'name' => 'Banana',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/sectors/{$sector->id}/products", [
                'name' => 'Banana',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Banana');

        $duplicateResponse = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/sectors/{$sector->id}/products", [
                'name' => 'Banana',
            ]);

        $duplicateResponse
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_rename_product_when_name_is_unique_within_sector(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);
        $otherSector = Sector::factory()->for($template)->create([
            'name' => 'Bakery',
            'order' => 2,
        ]);

        $product = Product::query()->create([
            'sector_id' => $sector->id,
            'name' => 'Old Name',
        ]);
        Product::query()->create([
            'sector_id' => $sector->id,
            'name' => 'Existing Name',
        ]);
        Product::query()->create([
            'sector_id' => $otherSector->id,
            'name' => 'Other Sector Name',
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}", [
                'name' => 'Other Sector Name',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Other Sector Name');

        $duplicateResponse = $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}", [
                'name' => 'Existing Name',
            ]);

        $duplicateResponse
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_delete_product(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);
        $product = Product::query()->create([
            'sector_id' => $sector->id,
            'name' => 'Banana',
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Produto removido com sucesso.')
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_user_cannot_operate_products_outside_their_templates_and_parent_mismatches_return_not_found(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $template = Template::factory()->for($user)->create();
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);
        $otherSector = Sector::factory()->for($template)->create([
            'name' => 'Bakery',
            'order' => 2,
        ]);
        $product = Product::query()->create([
            'sector_id' => $otherSector->id,
            'name' => 'Bread',
        ]);

        $otherUsersTemplate = Template::factory()->for($otherUser)->create();
        $otherUsersSector = Sector::factory()->for($otherUsersTemplate)->create([
            'name' => 'Frozen',
            'order' => 1,
        ]);
        $otherUsersProduct = Product::query()->create([
            'sector_id' => $otherUsersSector->id,
            'name' => 'Pizza',
        ]);

        $this
            ->actingAs($user)
            ->getJson("/api/templates/{$otherUsersTemplate->id}/sectors/{$otherUsersSector->id}/products")
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->postJson("/api/templates/{$otherUsersTemplate->id}/sectors/{$otherUsersSector->id}/products", ['name' => 'Milk'])
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$otherUsersTemplate->id}/sectors/{$otherUsersSector->id}/products/{$otherUsersProduct->id}", ['name' => 'Milk'])
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$otherUsersTemplate->id}/sectors/{$otherUsersSector->id}/products/{$otherUsersProduct->id}")
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}", ['name' => 'Milk'])
            ->assertNotFound();

        $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sector_id' => $otherSector->id,
            'name' => 'Bread',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $otherUsersProduct->id,
            'sector_id' => $otherUsersSector->id,
            'name' => 'Pizza',
        ]);
    }

    public function test_products_do_not_expose_reorder_endpoint(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);

        $this
            ->actingAs($user)
            ->putJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/reorder", ['ids' => []])
            ->assertNotFound();
    }

    public function test_product_changes_do_not_affect_existing_shopping_session_snapshots(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);
        $product = Product::query()->create([
            'sector_id' => $sector->id,
            'name' => 'Banana',
        ]);
        $snapshot = [
            'template' => [
                'id' => $template->id,
                'name' => 'Central Market',
                'sectors' => [
                    [
                        'id' => $sector->id,
                        'name' => 'Produce',
                        'order' => 1,
                        'products' => [
                            [
                                'id' => $product->id,
                                'name' => 'Banana',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        DB::table('shopping_sessions')->insert([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => 'active',
            'snapshot' => json_encode($snapshot),
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}", [
                'name' => 'Apple',
            ])
            ->assertOk();

        $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $persistedSnapshot = DB::table('shopping_sessions')->value('snapshot');

        $this->assertSame($snapshot, json_decode($persistedSnapshot, true));
    }
}
