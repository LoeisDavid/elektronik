<?php

namespace Tests\Feature;

use App\Models\KAS;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'USERNAME' => 'admin',
            'PASS'     => Hash::make('admin123'),
            'ROLE'     => 1,
        ]);
    }

    /**
     * Test bahwa jika kasir belum ditutup, POST /logout akan dialihkan ke POS.kasir dengan ?action=tutup_kasir
     */
    public function test_logout_redirects_when_cashier_is_open(): void
    {
        // 1. Buka shift kasir
        KAS::create([
            'CD'    => 'CD00000001',
            'TGL'   => now()->toDateString(),
            'USER'  => 'admin',
            'AWLT'  => 50000,
            'AWLNT' => 0,
            'DBT'   => 0,
            'DBNT'  => 0,
            'CRT'   => 0,
            'CRNT'  => 0,
            'AKHT'  => 50000,
            'AKHNT' => 0,
            'TGLM'  => now(),
            'TGLK'  => null, // Masih terbuka
        ]);

        $response = $this->actingAs($this->user)->post('/logout');

        // Harus dialihkan kembali ke POS.kasir beserta pesan error dan action tutup_kasir
        $response->assertRedirect(route('POS.kasir', ['action' => 'tutup_kasir']));
        $response->assertSessionHas('error');
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * Test bahwa setelah tutup kasir berhasil, POST /logout dapat langsung logout ke /login
     */
    public function test_logout_succeeds_immediately_after_tutup_kasir(): void
    {
        // 1. Buat shift kasir terbuka
        $kas = KAS::create([
            'CD'    => 'CD00000002',
            'TGL'   => now()->toDateString(),
            'USER'  => 'admin',
            'AWLT'  => 50000,
            'AWLNT' => 0,
            'DBT'   => 0,
            'DBNT'  => 0,
            'CRT'   => 0,
            'CRNT'  => 0,
            'AKHT'  => 50000,
            'AKHNT' => 0,
            'TGLM'  => now(),
            'TGLK'  => null,
        ]);

        // 2. Tutup kasir via endpoint POST /POS/tutup-kasir
        $tutupResponse = $this->actingAs($this->user)->postJson('/POS/tutup-kasir');
        $tutupResponse->assertOk();
        $tutupResponse->assertJson(['success' => true]);

        // Pastikan tidak ada kasir yang TGLK nya null
        $this->assertEquals(0, KAS::whereNull('TGLK')->count());

        // 3. Tekan Logout langsung (tanpa perlu refresh apapun)
        $logoutResponse = $this->actingAs($this->user)->post('/logout');

        // Harus dialihkan ke /login dan user sudah unauthenticated (guest)
        $logoutResponse->assertRedirect('/login');
        $this->assertGuest();
    }
}
