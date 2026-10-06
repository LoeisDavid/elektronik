<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk {{ $struk['ref_no'] }}</title>

    <style>
        @page {
            margin: 14px 12px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }

        .nama-toko {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000;
            margin: 0 0 2px 0;
        }

        .no-transaksi-label {
            font-size: 8px;
            color: #6b7280;
            margin: 2px 0 1px 0;
        }

        .no-transaksi-val {
            font-size: 10px;
            font-weight: bold;
            color: #111827;
            margin: 0;
        }

        .garis {
            border-top: 1px dashed #9ca3af;
            margin: 6px 0;
        }

        .label { color: #4b5563; }
        .value { color: #111827; }
        .text-discount { color: #dc2626; }

        .total-row td {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
            padding-top: 3px;
            padding-bottom: 3px;
            border-top: 1px solid #e5e7eb;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    {{-- HEADER TOKO --}}
    <div class="center">
        <p class="nama-toko">Pomelo.</p>
        <p class="no-transaksi-label">No. Transaksi</p>
        <p class="no-transaksi-val">{{ $struk['ref_no'] }}</p>
    </div>

    <div class="garis"></div>

    {{-- DETAIL TRANSAKSI --}}
    <div style="font-size: 8px; color: #4b5563; line-height: 1.4;">
        <div>{{ $struk['tanggal'] ?: date('d M Y') }}</div>
        @if(!empty($struk['waktu']))
            <div>{{ $struk['waktu'] }}</div>
        @endif
        <div>Dilayani oleh: <span class="bold value">{{ $struk['kasir'] ?: 'Kasir' }}</span></div>
    </div>

    <div class="garis"></div>

    {{-- ITEM / PRODUK --}}
    @if (count($struk['items']) === 0)
        <p class="center" style="color:#9ca3af;">Tidak ada detail produk</p>
    @else
        <table>
            @foreach ($struk['items'] as $item)
                <tr>
                    <td class="bold value">
                        {{ $item['nama'] }}
                        <div style="font-size: 8px; font-weight: normal; color: #6b7280;">
                            {{ $item['qty'] }}x Rp {{ number_format($item['price'] ?? ($item['subtotal'] / max(1, $item['qty'])), 0, ',', '.') }}
                        </div>
                        @if (!empty($item['discount_info']))
                            <div style="font-size: 7.5px; color: #dc2626;">
                                Diskon: -{{ $item['discount_info'] }}
                            </div>
                        @endif
                    </td>
                    <td class="right bold value" style="white-space: nowrap;">
                        @if ($item['subtotal'] !== null)
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <div class="garis"></div>

    {{-- RINCIAN TOTAL & PEMBAYARAN --}}
    <table>
        @if (!empty($struk['subtotal']))
            <tr>
                <td class="label">Subtotal</td>
                <td class="right bold value">Rp {{ number_format($struk['subtotal'], 0, ',', '.') }}</td>
            </tr>
        @endif

        @if (!empty($struk['discount_item']) && $struk['discount_item'] > 0)
            <tr>
                <td class="label">Diskon Per Item</td>
                <td class="right bold text-discount">- Rp {{ number_format($struk['discount_item'], 0, ',', '.') }}</td>
            </tr>
        @endif

        @if (!empty($struk['discount_global']) && $struk['discount_global'] > 0)
            <tr>
                <td class="label">Diskon Global</td>
                <td class="right bold text-discount">- Rp {{ number_format($struk['discount_global'], 0, ',', '.') }}</td>
            </tr>
        @endif

        @if (!empty($struk['voucher']) && $struk['voucher'] > 0)
            <tr>
                <td class="label">Voucher</td>
                <td class="right bold text-discount">- Rp {{ number_format($struk['voucher'], 0, ',', '.') }}</td>
            </tr>
        @endif

        <tr class="total-row">
            <td>Total</td>
            <td class="right">Rp {{ number_format($struk['total'], 0, ',', '.') }}</td>
        </tr>

        @if (!empty($struk['metode']))
            <tr>
                <td class="label">Pembayaran</td>
                <td class="right bold value">{{ strtoupper($struk['metode']) }}</td>
            </tr>
        @endif

        @if ($struk['dibayar'] !== null && $struk['dibayar'] > 0)
            <tr>
                <td class="label">Dibayar</td>
                <td class="right bold value">Rp {{ number_format($struk['dibayar'], 0, ',', '.') }}</td>
            </tr>
        @endif

        @if ($struk['kembalian'] !== null && $struk['kembalian'] > 0)
            <tr>
                <td class="label">Kembali</td>
                <td class="right bold value">Rp {{ number_format($struk['kembalian'], 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <div class="footer">
        Terima kasih atas kunjungan Anda
    </div>

</body>
</html>