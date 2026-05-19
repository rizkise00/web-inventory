<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
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

    public function test_guest_redirected_from_categories(): void
    {
        $this->get('/categories')->assertRedirect(route('login'));
    }

    public function test_approved_user_can_view_categories(): void
    {
        $this->actingAs($this->user())->get('/categories')->assertOk();
    }

    public function test_approved_user_can_create_category(): void
    {
        $this->actingAs($this->user())->post('/categories', ['name' => 'Electronics'])
            ->assertRedirect('/categories');

        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_cannot_create_duplicate_category(): void
    {
        Category::factory()->create(['name' => 'Electronics']);

        $this->actingAs($this->user())->post('/categories', ['name' => 'Electronics'])
            ->assertSessionHasErrors('name');
    }

    public function test_approved_user_can_update_category(): void
    {
        $user = $this->user();
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAs($user)->put("/categories/{$category->id}", ['name' => 'New Name'])
            ->assertRedirect('/categories');

        $this->assertEquals('New Name', $category->fresh()->name);
    }

    public function test_approved_user_can_delete_empty_category(): void
    {
        $user = $this->user();
        $category = Category::factory()->create();

        $this->actingAs($user)->delete("/categories/{$category->id}")
            ->assertRedirect('/categories');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_items(): void
    {
        $user = $this->user();
        $category = Category::factory()->create();
        Item::factory()->create(['category_id' => $category->id]);

        $this->actingAs($user)->delete("/categories/{$category->id}")
            ->assertRedirect('/categories')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_any_approved_user_can_export_categories(): void
    {
        $this->actingAs($this->user())->get('/categories/export')
            ->assertDownload('categories.xlsx');
    }

    public function test_manager_can_export_categories(): void
    {
        $this->actingAs($this->manager())->get('/categories/export')
            ->assertDownload('categories.xlsx');
    }

    // --- Manager CRUD tests ---

    public function test_manager_can_view_categories(): void
    {
        $this->actingAs($this->manager())->get('/categories')->assertOk();
    }

    public function test_manager_can_create_category(): void
    {
        $this->actingAs($this->manager())->post('/categories', ['name' => 'Manager Category'])
            ->assertRedirect('/categories');

        $this->assertDatabaseHas('categories', ['name' => 'Manager Category']);
    }

    public function test_manager_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old']);

        $this->actingAs($this->manager())->put("/categories/{$category->id}", ['name' => 'Updated'])
            ->assertRedirect('/categories');

        $this->assertEquals('Updated', $category->fresh()->name);
    }

    public function test_manager_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->manager())->delete("/categories/{$category->id}")
            ->assertRedirect('/categories');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
