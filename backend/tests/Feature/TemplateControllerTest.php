<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sector;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_only_their_templates_with_light_contract_ordered_by_recent_update(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $newerTemplate = Template::factory()->for($user)->create([
            'name' => 'Newer Market',
            'updated_at' => now(),
        ]);
        $olderTemplate = Template::factory()->for($user)->create([
            'name' => 'Older Market',
            'updated_at' => now()->subDay(),
        ]);
        Template::factory()->for($otherUser)->create([
            'name' => 'Other User Market',
            'updated_at' => now()->addDay(),
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/templates');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', null)
            ->assertJsonPath('data.0', [
                'id' => $newerTemplate->id,
                'name' => 'Newer Market',
            ])
            ->assertJsonPath('data.1', [
                'id' => $olderTemplate->id,
                'name' => 'Older Market',
            ])
            ->assertJsonMissing(['name' => 'Other User Market']);

        $response->assertJsonStructure(['success', 'message', 'data', 'links', 'meta']);

        $this->assertSame(
            ['id', 'name'],
            array_keys($response->json('data.0'))
        );
    }

    public function test_user_can_list_their_templates_ordered_by_name(): void
    {
        $user = User::factory()->create();

        Template::factory()->for($user)->create(['name' => 'Z Market']);
        Template::factory()->for($user)->create(['name' => 'A Market']);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/templates?sort=name&direction=asc');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'A Market')
            ->assertJsonPath('data.1.name', 'Z Market');
    }

    public function test_user_can_create_template_with_name_unique_to_their_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Template::factory()->for($otherUser)->create(['name' => 'Central Market']);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/templates', ['name' => 'Central Market']);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Template criado com sucesso.')
            ->assertJsonPath('data.name', 'Central Market');

        $duplicateResponse = $this
            ->actingAs($user)
            ->postJson('/api/templates', ['name' => 'Central Market']);

        $duplicateResponse
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_rename_template_when_name_is_unique_to_their_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $template = Template::factory()->for($user)->create(['name' => 'Old Name']);
        Template::factory()->for($user)->create(['name' => 'Existing Name']);
        Template::factory()->for($otherUser)->create(['name' => 'Other User Name']);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}", ['name' => 'Other User Name']);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Other User Name');

        $duplicateResponse = $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}", ['name' => 'Existing Name']);

        $duplicateResponse
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_view_template_detail_with_empty_sectors(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Empty Market']);

        $response = $this
            ->actingAs($user)
            ->getJson("/api/templates/{$template->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data', [
                'id' => $template->id,
                'name' => 'Empty Market',
                'sectors' => [],
            ]);
    }

    public function test_user_can_view_template_detail_with_ordered_sectors_and_products(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Full Market']);

        $secondSector = Sector::query()->create([
            'template_id' => $template->id,
            'name' => 'Checkout',
            'order' => 2,
        ]);
        $firstSector = Sector::query()->create([
            'template_id' => $template->id,
            'name' => 'Produce',
            'order' => 1,
        ]);
        Product::query()->create([
            'sector_id' => $firstSector->id,
            'name' => 'Banana',
            'created_at' => now()->subMinute(),
        ]);
        Product::query()->create([
            'sector_id' => $firstSector->id,
            'name' => 'Apple',
            'created_at' => now(),
        ]);
        Product::query()->create([
            'sector_id' => $secondSector->id,
            'name' => 'Chocolate',
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson("/api/templates/{$template->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.sectors.0.name', 'Produce')
            ->assertJsonPath('data.sectors.0.products.0.name', 'Banana')
            ->assertJsonPath('data.sectors.0.products.1.name', 'Apple')
            ->assertJsonPath('data.sectors.1.name', 'Checkout')
            ->assertJsonPath('data.sectors.1.products.0.name', 'Chocolate');
    }

    public function test_user_can_delete_template_and_its_structure_without_deleting_history_snapshots(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Archived Market']);
        $sector = Sector::query()->create([
            'template_id' => $template->id,
            'name' => 'Produce',
            'order' => 1,
        ]);
        $product = Product::query()->create([
            'sector_id' => $sector->id,
            'name' => 'Banana',
        ]);
        DB::table('shared_templates')->insert([
            'code' => 'SHARE123',
            'template_id' => $template->id,
            'snapshot' => json_encode(['name' => 'Archived Market']),
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('shopping_sessions')->insert([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => 'finished',
            'snapshot' => json_encode(['name' => 'Archived Market']),
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('purchase_histories')->insert([
            'user_id' => $user->id,
            'template_name' => 'Archived Market',
            'finished_at' => now(),
            'total' => 12.34,
            'sectors_summary' => json_encode([['name' => 'Produce']]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Template removido com sucesso.')
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('templates', ['id' => $template->id]);
        $this->assertDatabaseMissing('sectors', ['id' => $sector->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('shared_templates', ['template_id' => $template->id]);
        $this->assertDatabaseHas('shopping_sessions', [
            'user_id' => $user->id,
            'template_id' => null,
            'status' => 'finished',
        ]);
        $this->assertDatabaseHas('purchase_histories', [
            'user_id' => $user->id,
            'template_name' => 'Archived Market',
        ]);
    }

    public function test_user_cannot_view_rename_or_delete_another_users_template(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $template = Template::factory()->for($otherUser)->create(['name' => 'Private Market']);

        $this
            ->actingAs($user)
            ->getJson("/api/templates/{$template->id}")
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}", ['name' => 'Stolen Market'])
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('templates', [
            'id' => $template->id,
            'name' => 'Private Market',
        ]);
    }
}
