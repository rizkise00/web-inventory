<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()->manager()->create();
    }

    private function regular(): User
    {
        return User::factory()->create();
    }

    public function test_guest_redirected_from_users_index(): void
    {
        $this->get('/users')->assertRedirect(route('login'));
    }

    public function test_unapproved_manager_redirected_from_users_index(): void
    {
        $user = User::factory()->manager()->unapproved()->create();
        $this->actingAs($user)->get('/users')->assertRedirect(route('login'));
    }

    public function test_regular_approved_user_redirected_from_users(): void
    {
        $this->actingAs($this->regular())->get('/users')
            ->assertRedirect(route('dashboard'));
    }

    public function test_manager_can_view_users_list(): void
    {
        $this->actingAs($this->manager())->get('/users')->assertOk();
    }

    public function test_manager_can_view_create_user_form(): void
    {
        $this->actingAs($this->manager())->get('/users/create')->assertOk();
    }

    public function test_manager_can_create_user(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)->post('/users', [
            'name' => 'New Staff',
            'email' => 'staff@example.com',
            'password' => 'password',
            'role' => 'admin',
        ])->assertRedirect('/users');

        $this->assertDatabaseHas('users', ['email' => 'staff@example.com']);
    }

    public function test_manager_can_view_user_detail(): void
    {
        $manager = $this->manager();
        $user = User::factory()->create();

        $this->actingAs($manager)->get("/users/{$user->id}")->assertOk();
    }

    public function test_manager_can_edit_user(): void
    {
        $manager = $this->manager();
        $user = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($manager)->put("/users/{$user->id}", [
            'name' => 'New Name',
            'email' => $user->email,
            'role' => 'admin',
        ])->assertRedirect('/users');

        $this->assertEquals('New Name', $user->fresh()->name);
    }

    public function test_manager_can_approve_user(): void
    {
        $manager = $this->manager();
        $user = User::factory()->unapproved()->create();

        $this->actingAs($manager)
            ->post("/users/{$user->id}/approve")
            ->assertRedirect();

        $this->assertTrue($user->fresh()->is_approved);
    }

    public function test_non_manager_cannot_approve_user(): void
    {
        $regular = $this->regular();
        $user = User::factory()->unapproved()->create();

        $this->actingAs($regular)
            ->post("/users/{$user->id}/approve")
            ->assertRedirect(route('dashboard'));

        $this->assertFalse($user->fresh()->is_approved);
    }

    public function test_manager_can_reject_user(): void
    {
        $manager = $this->manager();
        $user = User::factory()->create();

        $this->actingAs($manager)
            ->post("/users/{$user->id}/reject")
            ->assertRedirect('/users');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_manager_cannot_reject_user(): void
    {
        $regular = $this->regular();
        $target = User::factory()->create();

        $this->actingAs($regular)
            ->post("/users/{$target->id}/reject")
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_manager_can_delete_user(): void
    {
        $manager = $this->manager();
        $user = User::factory()->create();

        $this->actingAs($manager)
            ->delete("/users/{$user->id}")
            ->assertRedirect('/users');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_manager_cannot_delete_user(): void
    {
        $regular = $this->regular();
        $target = User::factory()->create();

        $this->actingAs($regular)
            ->delete("/users/{$target->id}")
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_manager_cannot_delete_themselves(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)
            ->delete("/users/{$manager->id}")
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $manager->id]);
    }

    public function test_manager_can_export_users(): void
    {
        $this->actingAs($this->manager())->get('/users/export')
            ->assertDownload('users.xlsx');
    }

    public function test_non_manager_cannot_access_users_export(): void
    {
        $this->actingAs($this->regular())->get('/users/export')
            ->assertRedirect(route('dashboard'));
    }

    // --- PDF export tests ---

    public function test_manager_can_export_users_as_pdf(): void
    {
        $this->actingAs($this->manager())
            ->get('/users/export?format=pdf')
            ->assertDownload('users.pdf');
    }

    public function test_non_manager_cannot_export_users_as_pdf(): void
    {
        $this->actingAs($this->regular())
            ->get('/users/export?format=pdf')
            ->assertRedirect(route('dashboard'));
    }

    public function test_export_users_pdf_with_role_filter(): void
    {
        User::factory()->manager()->create();

        $this->actingAs($this->manager())
            ->get('/users/export?format=pdf&role=manager')
            ->assertDownload('users.pdf');
    }

    public function test_export_users_pdf_with_status_filter(): void
    {
        $this->actingAs($this->manager())
            ->get('/users/export?format=pdf&status=approved')
            ->assertDownload('users.pdf');
    }

    public function test_guest_cannot_export_users_pdf(): void
    {
        $this->get('/users/export?format=pdf')
            ->assertRedirect(route('login'));
    }

    // --- Admin (non-manager) blocked tests for all user management routes ---

    public function test_admin_cannot_access_create_user_form(): void
    {
        $this->actingAs($this->regular())->get('/users/create')
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_cannot_create_user(): void
    {
        $this->actingAs($this->regular())->post('/users', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password',
            'role' => 'admin',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    public function test_admin_cannot_view_user_detail(): void
    {
        $target = User::factory()->create();

        $this->actingAs($this->regular())->get("/users/{$target->id}")
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_cannot_access_edit_user_form(): void
    {
        $target = User::factory()->create();

        $this->actingAs($this->regular())->get("/users/{$target->id}/edit")
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_cannot_update_user(): void
    {
        $target = User::factory()->create(['name' => 'Original Name']);

        $this->actingAs($this->regular())->put("/users/{$target->id}", [
            'name' => 'Changed Name',
            'email' => $target->email,
            'role' => 'manager',
        ])->assertRedirect(route('dashboard'));

        $this->assertEquals('Original Name', $target->fresh()->name);
    }

    public function test_admin_cannot_delete_user(): void
    {
        $target = User::factory()->create();

        $this->actingAs($this->regular())->delete("/users/{$target->id}")
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }
}
