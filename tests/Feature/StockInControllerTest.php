<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockInControllerTest extends TestCase
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

    public function test_guest_redirected_from_stock_in(): void
    {
        $this->get('/stock-in')->assertRedirect(route('login'));
    }

    public function test_approved_user_can_view_stock_in_list(): void
    {
        $this->actingAs($this->user())->get('/stock-in')->assertOk();
    }

    public function test_approved_user_can_view_create_form(): void
    {
        $this->actingAs($this->user())->get('/stock-in/create')->assertOk();
    }

    public function test_approved_user_can_create_stock_in(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 0]);

        $this->actingAs($user)->post('/stock-in', [
            'item_id' => $item->id,
            'quantity' => 20,
        ])->assertRedirect('/stock-in');

        $this->assertDatabaseHas('stock_ins', ['item_id' => $item->id, 'quantity' => 20]);
    }

    public function test_stock_increments_after_stock_in(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 10]);

        $this->actingAs($user)->post('/stock-in', [
            'item_id' => $item->id,
            'quantity' => 15,
        ]);

        $this->assertEquals(25, $item->fresh()->stock);
    }

    public function test_stock_in_requires_valid_data(): void
    {
        $this->actingAs($this->user())->post('/stock-in', [
            'item_id' => '',
            'quantity' => 0,
        ])->assertSessionHasErrors(['item_id', 'quantity']);
    }

    public function test_approved_user_can_view_stock_in_detail(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 0]);
        $stockIn = StockIn::factory()->create(['item_id' => $item->id, 'quantity' => 10]);

        $this->actingAs($user)->get("/stock-in/{$stockIn->id}")->assertOk();
    }

    public function test_approved_user_can_update_stock_in_quantity(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 0]);
        // booted created hook increments stock to 10
        $stockIn = StockIn::factory()->create(['item_id' => $item->id, 'quantity' => 10]);

        $this->actingAs($user)->put("/stock-in/{$stockIn->id}", [
            'item_id' => $item->id,
            'quantity' => 20,
        ])->assertRedirect('/stock-in');

        // booted updated hook: diff = +10, increments stock from 10 to 20
        $this->assertEquals(20, $item->fresh()->stock);
    }

    public function test_stock_adjusts_when_stock_in_quantity_decreased(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 0]);
        $stockIn = StockIn::factory()->create(['item_id' => $item->id, 'quantity' => 20]);
        // stock = 20 after creation

        $this->actingAs($user)->put("/stock-in/{$stockIn->id}", [
            'item_id' => $item->id,
            'quantity' => 8,
        ]);

        // diff = -12, decrements stock from 20 to 8
        $this->assertEquals(8, $item->fresh()->stock);
    }

    public function test_approved_user_can_delete_stock_in(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 0]);
        $stockIn = StockIn::factory()->create(['item_id' => $item->id, 'quantity' => 15]);
        // stock = 15 after creation

        $this->actingAs($user)->delete("/stock-in/{$stockIn->id}")
            ->assertRedirect('/stock-in');

        // booted deleted hook: decrements stock by 15 → back to 0
        $this->assertEquals(0, $item->fresh()->stock);
        $this->assertDatabaseMissing('stock_ins', ['id' => $stockIn->id]);
    }

    public function test_manager_can_export_stock_in(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->get('/stock-in/export')
            ->assertDownload('stock-in.xlsx');
    }

    public function test_admin_can_export_stock_in(): void
    {
        $this->actingAs($this->user())->get('/stock-in/export')
            ->assertDownload('stock-in.xlsx');
    }

    public function test_export_respects_item_name_filter(): void
    {
        $this->actingAs($this->manager())->get('/stock-in/export?item_name=Laptop')
            ->assertDownload('stock-in.xlsx');
    }

    // --- Manager CRUD tests ---

    public function test_manager_can_view_stock_in_list(): void
    {
        $this->actingAs($this->manager())->get('/stock-in')->assertOk();
    }

    public function test_manager_can_create_stock_in(): void
    {
        $item = Item::factory()->create(['stock' => 0]);

        $this->actingAs($this->manager())->post('/stock-in', [
            'item_id' => $item->id,
            'quantity' => 30,
        ])->assertRedirect('/stock-in');

        $this->assertDatabaseHas('stock_ins', ['item_id' => $item->id, 'quantity' => 30]);
        $this->assertEquals(30, $item->fresh()->stock);
    }

    public function test_manager_can_view_stock_in_detail(): void
    {
        $item = Item::factory()->create(['stock' => 0]);
        $stockIn = StockIn::factory()->create(['item_id' => $item->id, 'quantity' => 5]);

        $this->actingAs($this->manager())->get("/stock-in/{$stockIn->id}")->assertOk();
    }

    public function test_manager_can_update_stock_in(): void
    {
        $item = Item::factory()->create(['stock' => 0]);
        $stockIn = StockIn::factory()->create(['item_id' => $item->id, 'quantity' => 10]);

        $this->actingAs($this->manager())->put("/stock-in/{$stockIn->id}", [
            'item_id' => $item->id,
            'quantity' => 25,
        ])->assertRedirect('/stock-in');

        $this->assertEquals(25, $item->fresh()->stock);
    }

    public function test_manager_can_delete_stock_in(): void
    {
        $item = Item::factory()->create(['stock' => 0]);
        $stockIn = StockIn::factory()->create(['item_id' => $item->id, 'quantity' => 10]);

        $this->actingAs($this->manager())->delete("/stock-in/{$stockIn->id}")
            ->assertRedirect('/stock-in');

        $this->assertEquals(0, $item->fresh()->stock);
        $this->assertDatabaseMissing('stock_ins', ['id' => $stockIn->id]);
    }

    // --- Date range filter tests ---

    public function test_date_from_filter_stock_in(): void
    {
        $user = $this->user();
        $itemOld = Item::factory()->create(['name' => 'OldItemStockIn', 'stock' => 0]);
        $itemNew = Item::factory()->create(['name' => 'NewItemStockIn', 'stock' => 0]);

        StockIn::factory()->create(['item_id' => $itemOld->id, 'created_at' => '2026-01-10 10:00:00']);
        StockIn::factory()->create(['item_id' => $itemNew->id, 'created_at' => '2026-05-20 10:00:00']);

        $response = $this->actingAs($user)->get('/stock-in?date_from=2026-05-01');

        $response->assertOk();
        $response->assertSee('NewItemStockIn');
        $response->assertDontSee('OldItemStockIn');
    }

    public function test_date_to_filter_stock_in(): void
    {
        $user = $this->user();
        $itemOld = Item::factory()->create(['name' => 'EarlyItemStockIn', 'stock' => 0]);
        $itemNew = Item::factory()->create(['name' => 'LateItemStockIn', 'stock' => 0]);

        StockIn::factory()->create(['item_id' => $itemOld->id, 'created_at' => '2026-01-10 10:00:00']);
        StockIn::factory()->create(['item_id' => $itemNew->id, 'created_at' => '2026-05-20 10:00:00']);

        $response = $this->actingAs($user)->get('/stock-in?date_to=2026-03-31');

        $response->assertOk();
        $response->assertSee('EarlyItemStockIn');
        $response->assertDontSee('LateItemStockIn');
    }

    public function test_date_range_filter_stock_in(): void
    {
        $user = $this->user();
        $itemA = Item::factory()->create(['name' => 'BeforeRangeStockIn', 'stock' => 0]);
        $itemB = Item::factory()->create(['name' => 'InRangeStockIn', 'stock' => 0]);
        $itemC = Item::factory()->create(['name' => 'AfterRangeStockIn', 'stock' => 0]);

        StockIn::factory()->create(['item_id' => $itemA->id, 'created_at' => '2026-01-05 10:00:00']);
        StockIn::factory()->create(['item_id' => $itemB->id, 'created_at' => '2026-03-15 10:00:00']);
        StockIn::factory()->create(['item_id' => $itemC->id, 'created_at' => '2026-06-01 10:00:00']);

        $response = $this->actingAs($user)->get('/stock-in?date_from=2026-03-01&date_to=2026-03-31');

        $response->assertOk();
        $response->assertSee('InRangeStockIn');
        $response->assertDontSee('BeforeRangeStockIn');
        $response->assertDontSee('AfterRangeStockIn');
    }

    public function test_export_respects_date_range_filter_stock_in(): void
    {
        $this->actingAs($this->user())
            ->get('/stock-in/export?date_from=2026-01-01&date_to=2026-12-31')
            ->assertDownload('stock-in.xlsx');
    }
}
