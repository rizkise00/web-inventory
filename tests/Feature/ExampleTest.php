<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_register_page_is_accessible(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_approved_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/')->assertOk();
    }

    public function test_unapproved_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->unapproved()->create();
        $this->actingAs($user)->get('/')->assertRedirect(route('login'));
    }
}
