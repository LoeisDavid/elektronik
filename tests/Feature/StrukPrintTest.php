<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StrukPrintTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        session(['selected_database' => env('DB_DATABASE', 'elektronik')]);

        $this->user = User::firstOrCreate(
            ['USERNAME' => 'admin'],
            [
                'PASS' => Hash::make('admin123'),
                'ROLE' => User::ROLE_ADMIN,
                'DES'  => 'Admin Test',
            ]
        );
    }

    private function sampleItems(int $count): array
    {
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $items[] = [
                'nama'          => "Produk Elektronik {$i}",
                'qty'           => 1,
                'price'         => 100000 * $i,
                'discount_info' => '',
                'subtotal'      => 100000 * $i,
            ];
        }
        return $items;
    }

    public function test_four_or_fewer_items_render_single_receipt_sheet_without_page_number(): void
    {
        $items = $this->sampleItems(4);

        $response = $this->actingAs($this->user)->post(route('POS.struk.print'), [
            'ref_no'          => 'INV0000101',
            'tanggal'         => '16 Nov 2026',
            'waktu'           => '14.30',
            'kasir'           => 'admin',
            'customer_nama'   => 'Budi Santoso',
            'customer_desa'   => 'Bangkal',
            'customer_alamat' => 'Jl. Diponegoro No. 48',
            'metode'          => 'Tunai',
            'subtotal'        => 1000000,
            'total'           => 1000000,
            'dibayar'         => 1100000,
            'kembalian'       => 100000,
            'items'           => json_encode($items),
        ]);

        $response->assertOk();
        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, '<article class="sheet">'));
        $this->assertSame(1, substr_count($html, '<section class="bottom-section">'));
        $this->assertStringNotContainsString('Hal.', $html);
        $this->assertStringContainsString('size: 215mm 110mm;', $html);
        $this->assertStringContainsString('window.print()', $html);
        $this->assertStringContainsString('Rp 1.000.000', $html);
        $this->assertStringContainsString('Rp 1.100.000', $html);
        $this->assertStringContainsString('Rp 100.000', $html);
        $this->assertStringContainsString('07.079.290.8.614.000', $html);
    }

    public function test_more_than_four_items_paginate_across_multiple_sheets_with_totals_on_last_sheet(): void
    {
        $items = $this->sampleItems(6);

        $response = $this->actingAs($this->user)->post(route('POS.struk.print'), [
            'ref_no'          => 'INV0000102',
            'tanggal'         => '16 Nov 2026',
            'waktu'           => '15.00',
            'kasir'           => 'admin',
            'customer_nama'   => 'Dewi Lestari',
            'customer_desa'   => 'Pamolokan',
            'customer_alamat' => 'Jl. Trunojoyo 12',
            'metode'          => 'Tunai',
            'subtotal'        => 2100000,
            'total'           => 2100000,
            'dibayar'         => 2100000,
            'kembalian'       => 0,
            'items'           => json_encode($items),
        ]);

        $response->assertOk();
        $html = $response->getContent();

        $this->assertSame(2, substr_count($html, '<article class="sheet">'));
        $this->assertSame(1, substr_count($html, '<section class="bottom-section">'));
        $this->assertStringContainsString('Hal. 1/2', $html);
        $this->assertStringContainsString('Hal. 2/2', $html);
        // 4 fixed rows per sheet across 2 sheets = 8 tbody rows
        $this->assertSame(8, substr_count($html, '<td class="center">') / 2);

        $secondSheetPos = strpos($html, 'Hal. 2/2');
        $bottomSectionPos = strpos($html, '<section class="bottom-section">');
        $this->assertNotFalse($secondSheetPos);
        $this->assertNotFalse($bottomSectionPos);
        $this->assertGreaterThan($secondSheetPos, $bottomSectionPos);
    }

    public function test_non_tunai_and_optional_discounts_voucher_ppn_render_correctly(): void
    {
        $items = [
            [
                'nama'          => 'Exhaust Fan 10 Inch',
                'qty'           => 1,
                'price'         => 200000,
                'discount_info' => 'Diskon: -10% (-Rp 20.000)',
                'subtotal'      => 180000,
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('POS.struk.print'), [
            'ref_no'           => 'INV0000103',
            'tanggal'          => '16 Nov 2026',
            'waktu'            => '16.00',
            'kasir'            => 'admin',
            'customer_nama'    => 'Andi',
            'customer_desa'    => 'Kota',
            'customer_alamat'  => 'Jl. Mawar',
            'metode'           => 'Non-Tunai / QRIS',
            'subtotal'         => 200000,
            'discount_item'    => 20000,
            'discount_percent' => 5,
            'discount_global'  => 9000,
            'voucher'          => 10000,
            'ppn'              => 11,
            'ppn_nominal'      => 17710,
            'total'            => 178710,
            'dibayar'          => 178710,
            'kembalian'        => 0,
            'items'            => json_encode($items),
        ]);

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Non-Tunai / QRIS', $html);
        $this->assertStringNotContainsString('<span class="label">Dibayar</span>', $html);
        $this->assertStringNotContainsString('<span class="label">Kembali</span>', $html);
        $this->assertStringContainsString('Diskon Per Item', $html);
        $this->assertStringContainsString('Diskon Global (5%)', $html);
        $this->assertStringContainsString('Voucher', $html);
        $this->assertStringContainsString('PPN (11%)', $html);
        $this->assertStringContainsString('has-extra-totals', $html);
        $this->assertStringContainsString('Rp 178.710', $html);
    }

    public function test_riwayat_transaksi_and_kasir_pages_render_updated_nota_modal(): void
    {
        $this->actingAs($this->user)
            ->get(route('POS.transaksi'))
            ->assertOk()
            ->assertSee('id="notaPreviewRoot"', false)
            ->assertSee('id="printDiscountPercent"', false);

        $this->actingAs($this->user)
            ->get(route('POS.kasir'))
            ->assertOk()
            ->assertSee('id="notaPreviewRoot"', false)
            ->assertSee('id="printDiscountPercent"', false);
    }
}
