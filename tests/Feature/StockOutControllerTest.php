<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOutControllerTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_guest_redirected_from_stock_out(): void
    {
        $this->get('/stock-out')->assertRedirect(route('login'));
    }

    public function test_approved_user_can_view_stock_out_list(): void
    {
        $this->actingAs($this->user())->get('/stock-out')->assertOk();
    }

    public function test_approved_user_can_view_create_form(): void
    {
        $this->actingAs($this->user())->get('/stock-out/create')->assertOk();
    }

    public function test_approved_user_can_create_stock_out(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 30]);

        $this->actingAs($user)->post('/stock-out', [
            'item_id' => $item->id,
            'quantity' => 10,
            'status' => 'Consumed',
        ])->assertRedirect('/stock-out');

        $this->assertEquals(20, $item->fresh()->stock);
    }

    public function test_cannot_create_stock_out_exceeding_stock(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 5]);

        $this->actingAs($user)->post('/stock-out', [
            'item_id' => $item->id,
            'quantity' => 10,
            'status' => 'Consumed',
        ])->assertSessionHasErrors('quantity');

        $this->assertEquals(5, $item->fresh()->stock);
    }

    public function test_stock_decrements_after_stock_out(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 50]);

        $this->actingAs($user)->post('/stock-out', [
            'item_id' => $item->id,
            'quantity' => 25,
            'status' => 'Damaged',
        ]);

        $this->assertEquals(25, $item->fresh()->stock);
    }

    public function test_stock_out_requires_valid_status(): void
    {
        $item = Item::factory()->create(['stock' => 10]);

        $this->actingAs($this->user())->post('/stock-out', [
            'item_id' => $item->id,
            'quantity' => 5,
            'status' => 'InvalidStatus',
        ])->assertSessionHasErrors('status');
    }

    public function test_approved_user_can_view_stock_out_detail(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 30]);
        $stockOut = StockOut::factory()->create(['item_id' => $item->id, 'quantity' => 5]);

        $this->actingAs($user)->get("/stock-out/{$stockOut->id}")->assertOk();
    }

    public function test_approved_user_can_update_stock_out(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 30]);
        // booted created hook decrements stock: 30 - 10 = 20
        $stockOut = StockOut::factory()->create(['item_id' => $item->id, 'quantity' => 10, 'status' => 'Consumed']);

        $this->actingAs($user)->put("/stock-out/{$stockOut->id}", [
            'item_id' => $item->id,
            'quantity' => 5,
            'status' => 'Consumed',
        ])->assertRedirect('/stock-out');

        // diff = 5 - 10 = -5, increments stock by 5: 20 + 5 = 25
        $this->assertEquals(25, $item->fresh()->stock);
    }

    public function test_cannot_update_stock_out_exceeding_available(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 20]);
        // booted created hook: 20 - 10 = 10
        $stockOut = StockOut::factory()->create(['item_id' => $item->id, 'quantity' => 10, 'status' => 'Consumed']);

        // available = current(10) + original(10) = 20, requesting 25 > 20
        $this->actingAs($user)->put("/stock-out/{$stockOut->id}", [
            'item_id' => $item->id,
            'quantity' => 25,
            'status' => 'Consumed',
        ])->assertSessionHasErrors('quantity');
    }

    public function test_stock_returns_after_stock_out_deleted(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 20]);
        // booted created hook: 20 - 8 = 12
        $stockOut = StockOut::factory()->create(['item_id' => $item->id, 'quantity' => 8, 'status' => 'Consumed']);

        $this->actingAs($user)->delete("/stock-out/{$stockOut->id}")
            ->assertRedirect('/stock-out');

        // booted deleted hook: increments by 8 → 12 + 8 = 20
        $this->assertEquals(20, $item->fresh()->stock);
    }

    public function test_manager_can_export_stock_out(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->get('/stock-out/export')
            ->assertDownload('stock-out.xlsx');
    }

    public function test_non_manager_cannot_export_stock_out(): void
    {
        $this->actingAs($this->user())->get('/stock-out/export')
            ->assertRedirect(route('dashboard'));
    }

    public function test_export_respects_status_filter(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->get('/stock-out/export?status=Consumed')
            ->assertDownload('stock-out.xlsx');
    }
}
