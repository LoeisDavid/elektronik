<?php

namespace Tests\Feature;

use App\Models\ITM;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Setup session database
        session(['selected_database' => env('DB_DATABASE', 'elektronik')]);
    }

    public function test_manager_permissions()
    {
        $manager = User::where('ROLE', User::ROLE_MANAGER)->first();
        if (!$manager) {
            $manager = User::create([
                'USERNAME' => 'test_mng',
                'PASS'     => Hash::make('secret'),
                'ROLE'     => User::ROLE_MANAGER,
                'DES'      => 'Test Manager',
                'HP'       => '0899999901',
            ]);
        }

        // Manager can access POS and Inventory
        $this->actingAs($manager)->get(route('POS.kasir'))->assertOk();
        $this->actingAs($manager)->get(route('inventory.item'))->assertOk();

        // Manager can access User Management
        $this->actingAs($manager)->get(route('POS.user'))->assertOk();

        // Manager CANNOT create a user with Manager role (ROLE = 4)
        $response = $this->actingAs($manager)->post(route('POS.user.store'), [
            'USERNAME' => 'new_manager',
            'PASS'     => 'password123',
            'DES'      => 'New Manager',
            'HP'       => '0899999902',
            'ROLE'     => User::ROLE_MANAGER,
        ]);
        $response->assertSessionHasErrors('ROLE');

        // Manager CAN create a user with Admin role (ROLE = 1)
        $uAdmin = 'adm_' . substr(uniqid(), 0, 10);
        $response = $this->actingAs($manager)->post(route('POS.user.store'), [
            'USERNAME' => $uAdmin,
            'PASS'     => 'password123',
            'DES'      => 'Created Admin ' . uniqid(),
            'HP'       => '08' . rand(10000000, 99999999),
            'ROLE'     => User::ROLE_ADMIN,
        ]);
        $response->assertRedirect(route('POS.user'));
        $this->assertDatabaseHas('users', ['USERNAME' => $uAdmin, 'ROLE' => User::ROLE_ADMIN]);

        // Manager can view/print Laporan Desa
        $this->actingAs($manager)->get(route('POS.customer.desa.cetak'))->assertOk();

        // Manager can view/print Laporan Transaksi
        $this->actingAs($manager)->get(route('POS.transaksi.cetak'))->assertOk();

        // Manager CANNOT delete products (only admin can)
        $item = ITM::first();
        if ($item) {
            $response = $this->actingAs($manager)->delete(route('inventory.item.destroy', ['id' => $item->ITM]));
            $response->assertRedirect();
            $response->assertSessionHas('error');
            $this->assertDatabaseHas('itm', ['ITM' => $item->ITM]);
        }
    }

    public function test_admin_permissions()
    {
        $admin = User::where('ROLE', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = User::create([
                'USERNAME' => 'test_adm',
                'PASS'     => Hash::make('secret'),
                'ROLE'     => User::ROLE_ADMIN,
                'DES'      => 'Test Admin',
                'HP'       => '0899999903',
            ]);
        }

        // Admin can access POS and Inventory
        $this->actingAs($admin)->get(route('POS.kasir'))->assertOk();
        $this->actingAs($admin)->get(route('inventory.item'))->assertOk();

        // Admin can access User Management
        $this->actingAs($admin)->get(route('POS.user'))->assertOk();

        // Admin CANNOT create user with Manager role (4)
        $response = $this->actingAs($admin)->post(route('POS.user.store'), [
            'USERNAME' => 'adm_mng_' . uniqid(),
            'PASS'     => 'password123',
            'DES'      => 'Admin Trying Manager',
            'HP'       => '08' . rand(10000000, 99999999),
            'ROLE'     => User::ROLE_MANAGER,
        ]);
        $response->assertSessionHasErrors('ROLE');

        // Admin CANNOT create user with Admin role (1)
        $response = $this->actingAs($admin)->post(route('POS.user.store'), [
            'USERNAME' => 'adm_adm_' . uniqid(),
            'PASS'     => 'password123',
            'DES'      => 'Admin Trying Admin',
            'HP'       => '08' . rand(10000000, 99999999),
            'ROLE'     => User::ROLE_ADMIN,
        ]);
        $response->assertSessionHasErrors('ROLE');

        // Admin CAN create Kasir (2) or Storage (3)
        $uKasir = 'ksr_' . substr(uniqid(), 0, 10);
        $response = $this->actingAs($admin)->post(route('POS.user.store'), [
            'USERNAME' => $uKasir,
            'PASS'     => 'password123',
            'DES'      => 'Created Kasir ' . uniqid(),
            'HP'       => '08' . rand(10000000, 99999999),
            'ROLE'     => User::ROLE_KASIR,
        ]);
        $response->assertRedirect(route('POS.user'));
        $this->assertDatabaseHas('users', ['USERNAME' => $uKasir, 'ROLE' => User::ROLE_KASIR]);

        // Admin can view/print Laporan Desa
        $this->actingAs($admin)->get(route('POS.customer.desa.cetak'))->assertOk();

        // Admin can view/print Laporan Transaksi
        $this->actingAs($admin)->get(route('POS.transaksi.cetak'))->assertOk();
    }

    public function test_storage_permissions()
    {
        $storage = User::where('ROLE', User::ROLE_STORAGE)->first();
        if (!$storage) {
            $storage = User::create([
                'USERNAME' => 'test_str',
                'PASS'     => Hash::make('secret'),
                'ROLE'     => User::ROLE_STORAGE,
                'DES'      => 'Test Storage',
                'HP'       => '0899999904',
            ]);
        }

        // Storage can access Inventory
        $this->actingAs($storage)->get(route('inventory.item'))->assertOk();

        // Storage CANNOT access POS (redirected to inventory.item)
        $response = $this->actingAs($storage)->get(route('POS.kasir'));
        $response->assertRedirect(route('inventory.item'));

        // Storage CANNOT access User Management
        $response = $this->actingAs($storage)->get(route('POS.user'));
        $response->assertRedirect(route('inventory.item'));

        // Storage CANNOT delete products
        $item = ITM::first();
        if ($item) {
            $response = $this->actingAs($storage)->delete(route('inventory.item.destroy', ['id' => $item->ITM]));
            $response->assertRedirect();
            $this->assertDatabaseHas('itm', ['ITM' => $item->ITM]);
        }

        // Storage CANNOT edit products
        if ($item) {
            $response = $this->actingAs($storage)->put(route('inventory.item.update', ['id' => $item->ITM]), [
                'ITM' => $item->ITM,
                'DES' => 'Edited Name',
                'MRK' => $item->MRK,
                'KTG' => $item->KTG,
                'HRG' => 10000,
            ]);
            $response->assertRedirect();
            $response->assertSessionHas('error');
        }
    }

    public function test_kasir_permissions()
    {
        $kasir = User::where('ROLE', User::ROLE_KASIR)->first();
        if (!$kasir) {
            $kasir = User::create([
                'USERNAME' => 'test_ksr',
                'PASS'     => Hash::make('secret'),
                'ROLE'     => User::ROLE_KASIR,
                'DES'      => 'Test Kasir',
                'HP'       => '0899999905',
            ]);
        }

        // Kasir can access POS
        $this->actingAs($kasir)->get(route('POS.kasir'))->assertOk();

        // Kasir CANNOT access Inventory (redirected to POS.kasir)
        $response = $this->actingAs($kasir)->get(route('inventory.item'));
        $response->assertRedirect(route('POS.kasir'));

        // Kasir CANNOT access User Management
        $response = $this->actingAs($kasir)->get(route('POS.user'));
        $response->assertRedirect(route('POS.kasir'));

        // Kasir CANNOT access Laporan Desa Cetak
        $response = $this->actingAs($kasir)->get(route('POS.customer.desa.cetak'));
        $response->assertRedirect(route('POS.kasir'));

        // Kasir CAN access Laporan Transaksi Cetak
        $response = $this->actingAs($kasir)->get(route('POS.transaksi.cetak'));
        $response->assertOk();

        // Kasir CANNOT edit transaksi
        $response = $this->actingAs($kasir)->get(route('POS.transaksi.edit', ['id' => 'INV0000001']));
        $response->assertRedirect(route('POS.kasir'));

        // Kasir CAN access Laporan Kas
        $this->actingAs($kasir)->get(route('POS.laporan'))->assertOk();
    }
}

