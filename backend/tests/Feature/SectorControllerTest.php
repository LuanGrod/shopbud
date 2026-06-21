<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sector;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_template_sectors_in_route_order(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();

        $secondSector = Sector::factory()->for($template)->create([
            'name' => 'Checkout',
            'order' => 2,
        ]);
        $firstSector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson("/api/templates/{$template->id}/sectors");

        $response
            ->assertOk()
            ->assertJsonPath('data.0', [
                'id' => $firstSector->id,
                'name' => 'Produce',
                'order' => 1,
            ])
            ->assertJsonPath('data.1', [
                'id' => $secondSector->id,
                'name' => 'Checkout',
                'order' => 2,
            ]);
    }

    public function test_user_can_create_sector_at_the_end_of_template_route_with_template_unique_name(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $otherTemplate = Template::factory()->for($user)->create();

        Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);
        Sector::factory()->for($template)->create([
            'name' => 'Bakery',
            'order' => 2,
        ]);
        Sector::factory()->for($otherTemplate)->create([
            'name' => 'Frozen',
            'order' => 1,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/sectors", ['name' => 'Frozen']);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Frozen')
            ->assertJsonPath('data.order', 3);

        $duplicateResponse = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/sectors", ['name' => 'Frozen']);

        $duplicateResponse
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_rename_sector_when_name_is_unique_within_template(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $otherTemplate = Template::factory()->for($user)->create();

        $sector = Sector::factory()->for($template)->create([
            'name' => 'Old Name',
            'order' => 1,
        ]);
        Sector::factory()->for($template)->create([
            'name' => 'Existing Name',
            'order' => 2,
        ]);
        Sector::factory()->for($otherTemplate)->create([
            'name' => 'Other Template Name',
            'order' => 1,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}", [
                'name' => 'Other Template Name',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Other Template Name')
            ->assertJsonPath('data.order', 1);

        $duplicateResponse = $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}", [
                'name' => 'Existing Name',
            ]);

        $duplicateResponse
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_delete_sector_and_its_products(): void
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
            ->deleteJson("/api/templates/{$template->id}/sectors/{$sector->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Setor removido com sucesso.')
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('sectors', ['id' => $sector->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_user_can_reorder_template_sectors_with_complete_template_only_id_list(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $otherTemplate = Template::factory()->for($user)->create();

        $produce = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 10,
        ]);
        $bakery = Sector::factory()->for($template)->create([
            'name' => 'Bakery',
            'order' => 20,
        ]);
        $checkout = Sector::factory()->for($template)->create([
            'name' => 'Checkout',
            'order' => 30,
        ]);
        $otherSector = Sector::factory()->for($otherTemplate)->create([
            'name' => 'Frozen',
            'order' => 1,
        ]);

        $response = $this
            ->actingAs($user)
            ->putJson("/api/templates/{$template->id}/sectors/reorder", [
                'ids' => [$checkout->id, $produce->id, $bakery->id],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Setores reordenados com sucesso.')
            ->assertJsonPath('data.0.id', $checkout->id)
            ->assertJsonPath('data.0.order', 1)
            ->assertJsonPath('data.1.id', $produce->id)
            ->assertJsonPath('data.1.order', 2)
            ->assertJsonPath('data.2.id', $bakery->id)
            ->assertJsonPath('data.2.order', 3)
            ->assertJsonMissingPath('data.0.products');

        $this->assertDatabaseHas('sectors', ['id' => $checkout->id, 'order' => 1]);
        $this->assertDatabaseHas('sectors', ['id' => $produce->id, 'order' => 2]);
        $this->assertDatabaseHas('sectors', ['id' => $bakery->id, 'order' => 3]);

        $this
            ->actingAs($user)
            ->putJson("/api/templates/{$template->id}/sectors/reorder", [
                'ids' => [$checkout->id, $produce->id],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids']);

        $this
            ->actingAs($user)
            ->putJson("/api/templates/{$template->id}/sectors/reorder", [
                'ids' => [$checkout->id, $produce->id, $bakery->id, $otherSector->id],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids']);
    }

    public function test_user_cannot_operate_sectors_from_another_users_template(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $template = Template::factory()->for($otherUser)->create();
        $sector = Sector::factory()->for($template)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);

        $this
            ->actingAs($user)
            ->getJson("/api/templates/{$template->id}/sectors")
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/sectors", ['name' => 'Bakery'])
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}", ['name' => 'Bakery'])
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}/sectors/{$sector->id}")
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->putJson("/api/templates/{$template->id}/sectors/reorder", ['ids' => [$sector->id]])
            ->assertForbidden();

        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'name' => 'Produce',
            'order' => 1,
        ]);
    }

    public function test_sector_routes_return_not_found_when_sector_does_not_belong_to_parent_template(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create();
        $otherTemplate = Template::factory()->for($user)->create();
        $sector = Sector::factory()->for($otherTemplate)->create([
            'name' => 'Produce',
            'order' => 1,
        ]);

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}", ['name' => 'Bakery'])
            ->assertNotFound();

        $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}/sectors/{$sector->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'template_id' => $otherTemplate->id,
            'name' => 'Produce',
        ]);
    }
}
