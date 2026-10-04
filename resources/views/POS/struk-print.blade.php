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
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #000;
            margin: 0 0 3px 0;
        }

        .alamat {
            font-size: 8px;
            color: #6b7280;
            line-height: 1.4;
            margin: 0;
        }

        .garis {
            border-top: 1px dashed #9ca3af;
            margin: 7px 0;
        }

        .label { color: #4b5563; }
        .value { color: #111827; }

        .total-row td {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
            padding-bottom: 4px;
        }

        .tunai-first td {
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
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
        <p class="nama-toko">NamaToko</p>
        <p class="alamat">
            Medokan Ayu, Kec. Rungkut, Surabaya<br>
            No Telp: 0881-0268-77983
        </p>
    </div>

    <div class="garis"></div>

    {{-- DETAIL TRANSAKSI --}}
    <table>
        <tr>
            <td class="label">No. Transaksi</td>
            <td class="right bold value">{{ $struk['ref_no'] }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="right label">{{ $struk['tanggal'] ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Dilayani oleh</td>
            <td class="right label">{{ $struk['kasir'] ?: '-' }}</td>
        </tr>
    </table>

    <div class="garis"></div>

    {{-- ITEM / PRODUK --}}
    @if (count($struk['items']) === 0)

        <p class="center" style="color:#9ca3af;">Tidak ada detail produk</p>

    @else

        <table>
            @foreach ($struk['items'] as $item)
                <tr>
                    <td class="bold value">
                        {{ $item['qty'] }}x {{ $item['nama'] }}
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

    {{-- TOTAL & PEMBAYARAN --}}
    <table>
        <tr class="total-row">
            <td>Total</td>
            <td class="right">Rp {{ number_format($struk['total'], 0, ',', '.') }}</td>
        </tr>

        @if (!empty($struk['metode']))
            <tr>
                <td class="label">Metode pembayaran</td>
                <td class="right bold value">{{ strtoupper($struk['metode']) }}</td>
            </tr>
        @endif

        @if ($struk['dibayar'] !== null)
            <tr class="tunai-first">
                <td class="label">Dibayar</td>
                <td class="right bold value">Rp {{ number_format($struk['dibayar'], 0, ',', '.') }}</td>
            </tr>
        @endif

        @if ($struk['kembalian'] !== null)
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