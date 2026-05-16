<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceControllerTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function postMaintenance(User $user, Item $item, array $overrides = []): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($user)->post('/maintenances', array_merge([
            'item_id' => $item->id,
            'quantity' => 3,
            'date' => '2026-05-08',
            'status' => 'Pending',
        ], $overrides));
    }

    public function test_guest_redirected_from_maintenances(): void
    {
        $this->get('/maintenances')->assertRedirect(route('login'));
    }

    public function test_approved_user_can_view_maintenances(): void
    {
        $this->actingAs($this->user())->get('/maintenances')->assertOk();
    }

    public function test_approved_user_can_view_create_form(): void
    {
        $this->actingAs($this->user())->get('/maintenances/create')->assertOk();
    }

    public function test_creating_pending_maintenance_decrements_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);

        $this->postMaintenance($user, $item, ['quantity' => 3, 'status' => 'Pending'])
            ->assertRedirect('/maintenances');

        $this->assertEquals(7, $item->fresh()->stock);
    }

    public function test_creating_completed_maintenance_does_not_decrement_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);

        $this->postMaintenance($user, $item, ['quantity' => 3, 'status' => 'Completed'])
            ->assertRedirect('/maintenances');

        $this->assertEquals(10, $item->fresh()->stock);
    }

    public function test_cannot_create_maintenance_with_insufficient_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 2]);

        $this->postMaintenance($user, $item, ['quantity' => 5, 'status' => 'Pending'])
            ->assertSessionHasErrors('quantity');

        $this->assertEquals(2, $item->fresh()->stock);
    }

    public function test_approved_user_can_view_maintenance_detail(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);
        $this->postMaintenance($user, $item);
        $maintenance = Maintenance::first();

        $this->actingAs($user)->get("/maintenances/{$maintenance->id}")->assertOk();
    }

    public function test_updating_to_completed_returns_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);

        $this->postMaintenance($user, $item, ['quantity' => 3, 'status' => 'Pending']);
        $this->assertEquals(7, $item->fresh()->stock);

        $maintenance = Maintenance::first();

        $this->actingAs($user)->put("/maintenances/{$maintenance->id}", [
            'item_id' => $item->id,
            'quantity' => 3,
            'date' => '2026-05-08',
            'status' => 'Completed',
        ])->assertRedirect('/maintenances');

        $this->assertEquals(10, $item->fresh()->stock);
    }

    public function test_updating_from_completed_to_pending_decrements_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);

        $this->postMaintenance($user, $item, ['quantity' => 3, 'status' => 'Completed']);
        $this->assertEquals(10, $item->fresh()->stock);

        $maintenance = Maintenance::first();

        $this->actingAs($user)->put("/maintenances/{$maintenance->id}", [
            'item_id' => $item->id,
            'quantity' => 3,
            'date' => '2026-05-08',
            'status' => 'Pending',
        ])->assertRedirect('/maintenances');

        $this->assertEquals(7, $item->fresh()->stock);
    }

    public function test_deleting_pending_maintenance_returns_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);

        $this->postMaintenance($user, $item, ['quantity' => 4, 'status' => 'Pending']);
        $this->assertEquals(6, $item->fresh()->stock);

        $maintenance = Maintenance::first();

        $this->actingAs($user)->delete("/maintenances/{$maintenance->id}")
            ->assertRedirect('/maintenances');

        $this->assertEquals(10, $item->fresh()->stock);
    }

    public function test_deleting_completed_maintenance_does_not_return_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);

        $this->postMaintenance($user, $item, ['quantity' => 3, 'status' => 'Completed']);
        $this->assertEquals(10, $item->fresh()->stock);

        $maintenance = Maintenance::first();

        $this->actingAs($user)->delete("/maintenances/{$maintenance->id}")
            ->assertRedirect('/maintenances');

        $this->assertEquals(10, $item->fresh()->stock);
    }

    public function test_maintenance_requires_valid_data(): void
    {
        $this->actingAs($this->user())->post('/maintenances', [
            'item_id' => '',
            'quantity' => 0,
            'date' => 'not-a-date',
            'status' => 'InvalidStatus',
        ])->assertSessionHasErrors(['item_id', 'quantity', 'date', 'status']);
    }

    public function test_manager_can_export_maintenances(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->get('/maintenances/export')
            ->assertDownload('maintenances.xlsx');
    }

    public function test_non_manager_cannot_export_maintenances(): void
    {
        $this->actingAs($this->user())->get('/maintenances/export')
            ->assertRedirect(route('dashboard'));
    }
}
