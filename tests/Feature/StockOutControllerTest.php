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

    private function manager(): User
    {
        return User::factory()->manager()->create();
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

    public function test_admin_can_export_stock_out(): void
    {
        $this->actingAs($this->user())->get('/stock-out/export')
            ->assertDownload('stock-out.xlsx');
    }

    public function test_export_respects_status_filter(): void
    {
        $this->actingAs($this->manager())->get('/stock-out/export?status=Consumed')
            ->assertDownload('stock-out.xlsx');
    }

    // --- Manager CRUD tests ---

    public function test_manager_can_view_stock_out_list(): void
    {
        $this->actingAs($this->manager())->get('/stock-out')->assertOk();
    }

    public function test_manager_can_create_stock_out(): void
    {
        $item = Item::factory()->create(['stock' => 20]);

        $this->actingAs($this->manager())->post('/stock-out', [
            'item_id' => $item->id,
            'quantity' => 5,
            'status' => 'Consumed',
        ])->assertRedirect('/stock-out');

        $this->assertEquals(15, $item->fresh()->stock);
    }

    public function test_manager_can_view_stock_out_detail(): void
    {
        $item = Item::factory()->create(['stock' => 20]);
        $stockOut = StockOut::factory()->create(['item_id' => $item->id, 'quantity' => 5]);

        $this->actingAs($this->manager())->get("/stock-out/{$stockOut->id}")->assertOk();
    }

    public function test_manager_can_update_stock_out(): void
    {
        $item = Item::factory()->create(['stock' => 20]);
        // created hook: 20 - 10 = 10
        $stockOut = StockOut::factory()->create(['item_id' => $item->id, 'quantity' => 10, 'status' => 'Consumed']);

        $this->actingAs($this->manager())->put("/stock-out/{$stockOut->id}", [
            'item_id' => $item->id,
            'quantity' => 3,
            'status' => 'Damaged',
        ])->assertRedirect('/stock-out');

        // available was 10 + 10 = 20, new qty 3, stock = 20 - 3 = 17
        $this->assertEquals(17, $item->fresh()->stock);
    }

    public function test_manager_can_delete_stock_out(): void
    {
        $item = Item::factory()->create(['stock' => 20]);
        // created hook: 20 - 8 = 12
        $stockOut = StockOut::factory()->create(['item_id' => $item->id, 'quantity' => 8, 'status' => 'Consumed']);

        $this->actingAs($this->manager())->delete("/stock-out/{$stockOut->id}")
            ->assertRedirect('/stock-out');

        // deleted hook: 12 + 8 = 20
        $this->assertEquals(20, $item->fresh()->stock);
        $this->assertDatabaseMissing('stock_outs', ['id' => $stockOut->id]);
    }

    // --- Date range filter tests ---

    public function test_date_from_filter_stock_out(): void
    {
        $user = $this->user();
        $itemOld = Item::factory()->create(['name' => 'OldItemStockOut', 'stock' => 20]);
        $itemNew = Item::factory()->create(['name' => 'NewItemStockOut', 'stock' => 20]);

        StockOut::factory()->create(['item_id' => $itemOld->id, 'quantity' => 1, 'status' => 'Consumed', 'created_at' => '2026-01-10 10:00:00']);
        StockOut::factory()->create(['item_id' => $itemNew->id, 'quantity' => 1, 'status' => 'Consumed', 'created_at' => '2026-05-20 10:00:00']);

        $response = $this->actingAs($user)->get('/stock-out?date_from=2026-05-01');

        $response->assertOk();
        $response->assertSee('NewItemStockOut');
        $response->assertDontSee('OldItemStockOut');
    }

    public function test_date_to_filter_stock_out(): void
    {
        $user = $this->user();
        $itemOld = Item::factory()->create(['name' => 'EarlyItemStockOut', 'stock' => 20]);
        $itemNew = Item::factory()->create(['name' => 'LateItemStockOut', 'stock' => 20]);

        StockOut::factory()->create(['item_id' => $itemOld->id, 'quantity' => 1, 'status' => 'Consumed', 'created_at' => '2026-01-10 10:00:00']);
        StockOut::factory()->create(['item_id' => $itemNew->id, 'quantity' => 1, 'status' => 'Consumed', 'created_at' => '2026-05-20 10:00:00']);

        $response = $this->actingAs($user)->get('/stock-out?date_to=2026-03-31');

        $response->assertOk();
        $response->assertSee('EarlyItemStockOut');
        $response->assertDontSee('LateItemStockOut');
    }

    public function test_date_range_filter_stock_out(): void
    {
        $user = $this->user();
        $itemA = Item::factory()->create(['name' => 'BeforeRangeStockOut', 'stock' => 20]);
        $itemB = Item::factory()->create(['name' => 'InRangeStockOut', 'stock' => 20]);
        $itemC = Item::factory()->create(['name' => 'AfterRangeStockOut', 'stock' => 20]);

        StockOut::factory()->create(['item_id' => $itemA->id, 'quantity' => 1, 'status' => 'Consumed', 'created_at' => '2026-01-05 10:00:00']);
        StockOut::factory()->create(['item_id' => $itemB->id, 'quantity' => 1, 'status' => 'Consumed', 'created_at' => '2026-03-15 10:00:00']);
        StockOut::factory()->create(['item_id' => $itemC->id, 'quantity' => 1, 'status' => 'Consumed', 'created_at' => '2026-06-01 10:00:00']);

        $response = $this->actingAs($user)->get('/stock-out?date_from=2026-03-01&date_to=2026-03-31');

        $response->assertOk();
        $response->assertSee('InRangeStockOut');
        $response->assertDontSee('BeforeRangeStockOut');
        $response->assertDontSee('AfterRangeStockOut');
    }

    public function test_export_respects_date_range_filter_stock_out(): void
    {
        $this->actingAs($this->user())
            ->get('/stock-out/export?date_from=2026-01-01&date_to=2026-12-31')
            ->assertDownload('stock-out.xlsx');
    }
}
