<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        User::firstOrCreate(
            ['USERNAME' => 'admin'],
            [
                'PASS' => Hash::make('admin123'),
                'ROLE' => 1,
            ]
        );
    }
    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Username:');
        $response->assertSee('Password:');
        $response->assertSee('LOGIN');
    }

    public function test_user_can_login_with_dummy_data(): void
    {
        $response = $this->post('/login', [
            'USERNAME' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('POS.kasir'));
        $this->assertAuthenticated();
    }

    public function test_dashboard_displays_logged_in_user(): void
    {
        $user = User::where('USERNAME', 'admin')->first();

        $response = $this->actingAs($user)->get(route('POS.kasir'));
        $response->assertStatus(200);
        $response->assertSee('Kasir');
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('USERNAME', 'admin')->first();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
