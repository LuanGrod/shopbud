<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sector;
use App\Models\SharedTemplate;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SharedTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_share_their_template_with_a_temporary_code(): void
    {
        $this->travelTo(now()->startOfSecond());

        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        $sector = Sector::factory()->for($template)->create(['name' => 'Produce', 'order' => 1]);
        Product::factory()->for($sector)->create(['name' => 'Banana']);
        Product::factory()->for($sector)->create(['name' => 'Apple']);

        $response = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share");

        $response
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.code')
                ->whereType('data.code', 'string')
                ->where('data.expires_at', now()->addDay()->toJSON())
                ->where('data.template.name', 'Central Market')
                ->where('data.template.sectors_count', 1)
                ->where('data.template.products_count', 2)
                ->where('success', true)
                ->where('message', 'Template compartilhado com sucesso.')
                ->etc()
            );
    }

    public function test_user_cannot_share_another_users_template(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $template = Template::factory()->for($otherUser)->create(['name' => 'Private Market']);

        $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertForbidden();

        $this->assertDatabaseMissing('shared_templates', [
            'template_id' => $template->id,
        ]);
    }

    public function test_shared_template_expires_twenty_four_hours_after_creation(): void
    {
        $this->travelTo(now()->startOfSecond());

        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);

        $code = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $sharedTemplate = SharedTemplate::query()->where('code', $code)->firstOrFail();

        $this->assertSame(now()->addDay()->toJSON(), $sharedTemplate->expires_at->toJSON());
    }

    public function test_share_codes_are_unique_and_not_short_predictable_values(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);

        $firstCode = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $secondCode = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $this->assertNotSame($firstCode, $secondCode);
        $this->assertGreaterThanOrEqual(32, strlen($firstCode));
        $this->assertGreaterThanOrEqual(32, strlen($secondCode));
        $this->assertDatabaseHas('shared_templates', ['code' => $firstCode]);
        $this->assertDatabaseHas('shared_templates', ['code' => $secondCode]);
    }

    public function test_shared_template_snapshot_is_ordered_and_does_not_change_after_original_template_edits(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        $checkout = Sector::factory()->for($template)->create(['name' => 'Checkout', 'order' => 2]);
        $produce = Sector::factory()->for($template)->create(['name' => 'Produce', 'order' => 1]);
        $banana = Product::factory()->for($produce)->create([
            'name' => 'Banana',
            'created_at' => now()->subMinute(),
        ]);
        $apple = Product::factory()->for($produce)->create([
            'name' => 'Apple',
            'created_at' => now(),
        ]);
        $chocolate = Product::factory()->for($checkout)->create(['name' => 'Chocolate']);

        $code = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $expectedSnapshot = [
            'name' => 'Central Market',
            'sectors' => [
                [
                    'id' => $produce->id,
                    'name' => 'Produce',
                    'order' => 1,
                    'products' => [
                        ['id' => $banana->id, 'name' => 'Banana'],
                        ['id' => $apple->id, 'name' => 'Apple'],
                    ],
                ],
                [
                    'id' => $checkout->id,
                    'name' => 'Checkout',
                    'order' => 2,
                    'products' => [
                        ['id' => $chocolate->id, 'name' => 'Chocolate'],
                    ],
                ],
            ],
        ];

        $sharedTemplate = SharedTemplate::query()->where('code', $code)->firstOrFail();
        $this->assertSame($expectedSnapshot, $sharedTemplate->snapshot);

        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}", ['name' => 'Renamed Market'])
            ->assertOk();
        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$produce->id}", ['name' => 'Fresh Produce'])
            ->assertOk();
        $this
            ->actingAs($user)
            ->patchJson("/api/templates/{$template->id}/sectors/{$produce->id}/products/{$banana->id}", ['name' => 'Green Banana'])
            ->assertOk();
        $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/sectors/{$produce->id}/products", ['name' => 'Orange'])
            ->assertCreated();

        $this->assertSame($expectedSnapshot, $sharedTemplate->refresh()->snapshot);
    }

    public function test_deleting_original_template_revokes_active_shared_templates(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);

        $code = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $this
            ->actingAs($user)
            ->deleteJson("/api/templates/{$template->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('shared_templates', [
            'code' => $code,
        ]);
    }

    public function test_user_can_import_template_from_valid_shared_template_code(): void
    {
        $owner = User::factory()->create();
        $importer = User::factory()->create();
        $template = Template::factory()->for($owner)->create(['name' => 'Central Market']);
        $checkout = Sector::factory()->for($template)->create(['name' => 'Checkout', 'order' => 2]);
        $produce = Sector::factory()->for($template)->create(['name' => 'Produce', 'order' => 1]);
        Product::factory()->for($produce)->create([
            'name' => 'Banana',
            'created_at' => now()->subMinute(),
        ]);
        Product::factory()->for($produce)->create([
            'name' => 'Apple',
            'created_at' => now(),
        ]);
        Product::factory()->for($checkout)->create(['name' => 'Chocolate']);

        $code = $this
            ->actingAs($owner)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $response = $this
            ->actingAs($importer)
            ->postJson('/api/shared-templates/import', ['code' => $code]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Central Market')
            ->assertJsonPath('data.sectors.0.name', 'Produce')
            ->assertJsonPath('data.sectors.0.order', 1)
            ->assertJsonPath('data.sectors.0.products.0.name', 'Banana')
            ->assertJsonPath('data.sectors.0.products.1.name', 'Apple')
            ->assertJsonPath('data.sectors.1.name', 'Checkout')
            ->assertJsonPath('data.sectors.1.order', 2)
            ->assertJsonPath('data.sectors.1.products.0.name', 'Chocolate');

        $importedTemplate = Template::query()->findOrFail($response->json('data.id'));

        $this->assertTrue($importedTemplate->user->is($importer));
    }

    public function test_import_requires_authentication(): void
    {
        $this
            ->postJson('/api/shared-templates/import', ['code' => 'SHARE123'])
            ->assertUnauthorized();
    }

    public function test_user_cannot_import_with_missing_expired_or_revoked_code(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $template = Template::factory()->for($owner)->create(['name' => 'Central Market']);

        $expiredSharedTemplate = SharedTemplate::query()->create([
            'template_id' => $template->id,
            'code' => 'EXPIRED-CODE',
            'snapshot' => ['name' => 'Expired Market', 'sectors' => []],
            'expires_at' => now()->subSecond(),
        ]);

        $revokedCode = $this
            ->actingAs($owner)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $this
            ->actingAs($owner)
            ->deleteJson("/api/templates/{$template->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        foreach (['MISSING-CODE', $expiredSharedTemplate->code, $revokedCode] as $code) {
            $this
                ->actingAs($user)
                ->postJson('/api/shared-templates/import', ['code' => $code])
                ->assertUnprocessable()
                ->assertJsonValidationErrors(['code']);
        }

        $this->assertDatabaseMissing('templates', [
            'user_id' => $user->id,
        ]);
    }

    public function test_import_resolves_template_name_conflicts_predictably(): void
    {
        $user = User::factory()->create();
        $template = Template::factory()->for($user)->create(['name' => 'Central Market']);
        Template::factory()->for($user)->create(['name' => 'Central Market (Imported)']);

        $code = $this
            ->actingAs($user)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $response = $this
            ->actingAs($user)
            ->postJson('/api/shared-templates/import', ['code' => $code]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Central Market (Imported 2)');

        $this->assertDatabaseHas('templates', [
            'user_id' => $user->id,
            'name' => 'Central Market (Imported 2)',
        ]);
    }

    public function test_imported_template_stays_independent_after_original_changes_and_shared_template_revocation(): void
    {
        $owner = User::factory()->create();
        $importer = User::factory()->create();
        $template = Template::factory()->for($owner)->create(['name' => 'Central Market']);
        $sector = Sector::factory()->for($template)->create(['name' => 'Produce', 'order' => 1]);
        $product = Product::factory()->for($sector)->create(['name' => 'Banana']);

        $code = $this
            ->actingAs($owner)
            ->postJson("/api/templates/{$template->id}/share")
            ->assertCreated()
            ->json('data.code');

        $importedTemplateId = $this
            ->actingAs($importer)
            ->postJson('/api/shared-templates/import', ['code' => $code])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Central Market')
            ->assertJsonPath('data.sectors.0.name', 'Produce')
            ->assertJsonPath('data.sectors.0.products.0.name', 'Banana')
            ->assertJsonMissingPath('data.sectors.0.products.0.price')
            ->json('data.id');

        $this
            ->actingAs($owner)
            ->patchJson("/api/templates/{$template->id}", ['name' => 'Renamed Market'])
            ->assertOk();
        $this
            ->actingAs($owner)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}", ['name' => 'Fresh Produce'])
            ->assertOk();
        $this
            ->actingAs($owner)
            ->patchJson("/api/templates/{$template->id}/sectors/{$sector->id}/products/{$product->id}", ['name' => 'Green Banana'])
            ->assertOk();
        $this
            ->actingAs($owner)
            ->deleteJson("/api/templates/{$template->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('shared_templates', ['code' => $code]);

        $this
            ->actingAs($importer)
            ->getJson("/api/templates/{$importedTemplateId}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Central Market')
            ->assertJsonPath('data.sectors.0.name', 'Produce')
            ->assertJsonPath('data.sectors.0.products.0.name', 'Banana');

        $this
            ->actingAs($owner)
            ->getJson("/api/templates/{$importedTemplateId}")
            ->assertForbidden();
    }
}
