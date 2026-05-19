<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemControllerTest extends TestCase
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

    public function test_guest_redirected_from_items(): void
    {
        $this->get('/items')->assertRedirect(route('login'));
    }

    public function test_approved_user_can_view_items(): void
    {
        $this->actingAs($this->user())->get('/items')->assertOk();
    }

    public function test_approved_user_can_view_create_form(): void
    {
        $this->actingAs($this->user())->get('/items/create')->assertOk();
    }

    public function test_approved_user_can_create_item(): void
    {
        $user = $this->user();
        $category = Category::factory()->create();

        $this->actingAs($user)->post('/items', [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'price' => 500000,
            'description' => 'A test product',
        ])->assertRedirect('/items');

        $this->assertDatabaseHas('items', ['name' => 'Test Product', 'stock' => 0]);
    }

    public function test_item_creation_requires_valid_data(): void
    {
        $this->actingAs($this->user())->post('/items', [
            'name' => '',
            'category_id' => 999,
            'price' => 'invalid',
        ])->assertSessionHasErrors(['name', 'category_id', 'price']);
    }

    public function test_approved_user_can_view_item(): void
    {
        $user = $this->user();
        $item = Item::factory()->create();

        $this->actingAs($user)->get("/items/{$item->id}")->assertOk();
    }

    public function test_approved_user_can_update_item(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['name' => 'Old Name']);

        $this->actingAs($user)->put("/items/{$item->id}", [
            'name' => 'New Name',
            'category_id' => $item->category_id,
            'price' => $item->price,
        ])->assertRedirect('/items');

        $this->assertEquals('New Name', $item->fresh()->name);
    }

    public function test_stock_cannot_be_modified_via_item_update(): void
    {
        $user = $this->user();
        $item = Item::factory()->create(['stock' => 0]);

        $this->actingAs($user)->put("/items/{$item->id}", [
            'name' => $item->name,
            'category_id' => $item->category_id,
            'price' => $item->price,
            'stock' => 9999,
        ])->assertRedirect('/items');

        $this->assertEquals(0, $item->fresh()->stock);
    }

    public function test_approved_user_can_delete_item(): void
    {
        $user = $this->user();
        $item = Item::factory()->create();

        $this->actingAs($user)->delete("/items/{$item->id}")
            ->assertRedirect('/items');

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_any_approved_user_can_export_items(): void
    {
        $this->actingAs($this->user())->get('/items/export')
            ->assertDownload('products.xlsx');
    }

    public function test_manager_can_export_items(): void
    {
        $this->actingAs($this->manager())->get('/items/export')
            ->assertDownload('products.xlsx');
    }

    public function test_export_items_respects_search_filter(): void
    {
        $this->actingAs($this->user())->get('/items/export?search=Laptop')
            ->assertDownload('products.xlsx');
    }

    // --- Manager CRUD tests ---

    public function test_manager_can_view_items(): void
    {
        $this->actingAs($this->manager())->get('/items')->assertOk();
    }

    public function test_manager_can_view_create_item_form(): void
    {
        $this->actingAs($this->manager())->get('/items/create')->assertOk();
    }

    public function test_manager_can_create_item(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->manager())->post('/items', [
            'name' => 'Manager Product',
            'category_id' => $category->id,
            'price' => 250000,
            'description' => 'Created by manager',
        ])->assertRedirect('/items');

        $this->assertDatabaseHas('items', ['name' => 'Manager Product']);
    }

    public function test_manager_can_view_item(): void
    {
        $item = Item::factory()->create();
        $this->actingAs($this->manager())->get("/items/{$item->id}")->assertOk();
    }

    public function test_manager_can_update_item(): void
    {
        $item = Item::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->manager())->put("/items/{$item->id}", [
            'name' => 'Updated by Manager',
            'category_id' => $item->category_id,
            'price' => $item->price,
        ])->assertRedirect('/items');

        $this->assertEquals('Updated by Manager', $item->fresh()->name);
    }

    public function test_manager_can_delete_item(): void
    {
        $item = Item::factory()->create();

        $this->actingAs($this->manager())->delete("/items/{$item->id}")
            ->assertRedirect('/items');

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }
}
