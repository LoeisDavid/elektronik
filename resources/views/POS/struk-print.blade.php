<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nota Penjualan - {{ $struk['ref_no'] }}</title>

    @include('POS.partials.nota-styles')

    <style>
        @page {
            size: 215mm 110mm;
            margin: 5mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #111111;
            background-color: #eceff2;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            padding: 0 16px 24px;
            overflow-x: auto;
        }

        .print-bar {
            width: 215mm;
            margin: 16px auto 10px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-toolbar {
            appearance: none;
            border: 1px solid #111111;
            background: #ffffff;
            color: #111111;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 18px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-toolbar:hover {
            background: #111111;
            color: #ffffff;
        }

        @media print {
            html, body {
                background: #ffffff;
                padding: 0;
                overflow: visible;
            }

            .print-bar,
            .nota-root .preprinted-guide {
                display: none !important;
            }

            #receipt-root.nota-root {
                display: block;
                min-width: 0;
                padding: 0;
                gap: 0;
            }

            .nota-root .sheet {
                width: 205mm;
                height: 100mm;
                padding: 0;
                margin: 0;
                box-shadow: none;
                background: transparent;
                overflow: hidden;
            }

            .nota-root .sheet-inner {
                padding: 25mm 32mm 0 9mm;
            }

            .nota-root .sheet + .sheet {
                break-before: page;
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    @php
        $shopName = strtoupper(config('perusahaan.nama_perusahaan', 'Toko Bogor'));
        $shopAddress = strtoupper(trim(collect([config('perusahaan.alamat'), config('perusahaan.kota')])->filter()->implode(' ')));
        $shopPhone = config('perusahaan.no_telp', '');
        $shopNpwp = config('perusahaan.npwp', '');

        $totalPages = count($pages);
        $fmtRp = fn ($val): string => ($val < 0 ? '-' : '') . 'Rp ' . number_format(abs(round((float) $val)), 0, ',', '.');
    @endphp

    <div class="print-bar">
        <button class="btn-toolbar" type="button" onclick="window.close()">Tutup</button>
        <button class="btn-toolbar" type="button" onclick="window.print()">Print</button>
    </div>

    <main id="receipt-root" class="nota-root" aria-label="Nota Penjualan">
        @foreach ($pages as $pageIndex => $pageItems)
            @php
                $isLastPage = ($pageIndex === $totalPages - 1);
                $startIndex = $pageIndex * $rowsPerPage;
                $hasExtraTotals = $isLastPage && (
                    ($struk['discount_item'] ?? 0) > 0 ||
                    ($struk['discount_global'] ?? 0) > 0 ||
                    ($struk['voucher'] ?? 0) > 0 ||
                    ($struk['ppn_nominal'] ?? 0) > 0
                );
            @endphp

            <article class="sheet">
                <div class="preprinted-guide" aria-hidden="true">
                    <div class="pp-header">
                        <div class="pp-shop">{{ $shopName }}</div>
                        @if ($shopAddress !== '')
                            <div class="pp-meta">{{ $shopAddress }}</div>
                        @endif
                        @if ($shopPhone !== '')
                            <div class="pp-meta">{{ $shopPhone }}</div>
                        @endif
                        @if ($shopNpwp !== '')
                            <div class="pp-meta">NPWP: {{ $shopNpwp }}</div>
                        @endif
                    </div>
                    <div class="pp-watermark">{{ $shopName }}</div>
                    <div class="pp-tagline">TERPERCAYA SEJAK TAHUN 1975</div>
                    <div class="pp-ppn">HARGA SUDAH TERMASUK PPN</div>
                    <div class="pp-right-strip">
                        <div class="pp-right-text">
                            PERHATIAN!<br />
                            PIHAK TOKO TIDAK BERTANGGUNG JAWAB JIKA ADA<br />
                            KEKURANGAN PERLENGKAPAN (REMOTE, KARTU GARANSI, DLL)<br />
                            SETELAH BARANG DIBAWA / KELUAR DARI TOKO<br />
                            <strong>PERIKSALAH DENGAN TELITI</strong>
                        </div>
                    </div>
                </div>

                <div class="sheet-inner{{ $hasExtraTotals ? ' has-extra-totals' : '' }}">
                    <div class="trx-row">
                        <span class="label">No. Transaksi: </span>
                        <span class="doc-trx mono">{{ $struk['ref_no'] }}</span>
                    </div>

                    <section class="customer-block">
                        <div class="customer-left">
                            @foreach ($customerRows as $row)
                                <div class="customer-row">
                                    <span class="label">{{ $row['label'] }}</span>
                                    <span class="value">{{ $row['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="customer-right">
                            <div>
                                <span class="value">
                                    {{ $struk['tanggal'] }} &bull; {{ $struk['waktu'] }}@if ($totalPages > 1) &bull; Hal. {{ $pageIndex + 1 }}/{{ $totalPages }}@endif
                                </span>
                            </div>
                            <div>
                                <span class="label">Dilayani oleh: </span>
                                <span class="value">{{ $struk['kasir'] }}</span>
                            </div>
                        </div>
                    </section>

                    <section class="items-section">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th class="col-no center">No</th>
                                    <th class="col-name">Nama Barang</th>
                                    <th class="col-qty center">Qty</th>
                                    <th class="col-price num">Harga</th>
                                    <th class="col-total num">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($rowOffset = 0; $rowOffset < $rowsPerPage; $rowOffset++)
                                    @php $item = $pageItems[$rowOffset] ?? null; @endphp
                                    @if (!$item)
                                        <tr>
                                            <td class="center">&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td class="center">&nbsp;</td>
                                            <td class="num">&nbsp;</td>
                                            <td class="num">&nbsp;</td>
                                        </tr>
                                    @else
                                        @php
                                            $qty = (int) ($item['qty'] ?? 1);
                                            $unitPrice = (float) ($item['price'] ?? 0);
                                            $lineTotal = $item['subtotal'] !== null ? (float) $item['subtotal'] : ($qty * $unitPrice);
                                        @endphp
                                        <tr>
                                            <td class="center">{{ $startIndex + $rowOffset + 1 }}</td>
                                            <td title="{{ $item['nama'] }}">
                                                {{ $item['nama'] }}@if (!empty($item['discount_info']))<span class="item-disc-inline">({{ $item['discount_info'] }})</span>@endif
                                            </td>
                                            <td class="center">{{ $qty }}</td>
                                            <td class="num">{{ $fmtRp($unitPrice) }}</td>
                                            <td class="num">{{ $fmtRp($lineTotal) }}</td>
                                        </tr>
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </section>

                    @if ($isLastPage)
                        <section class="bottom-section">
                            <div class="payment-totals">
                                <div class="payment-left">
                                    <span class="label">Pembayaran</span>
                                    <span class="value num">{{ $struk['metode'] }}</span>
                                    @if ($isTunai)
                                        <span class="label">Dibayar</span>
                                        <span class="value num">{{ $fmtRp($struk['dibayar'] ?? 0) }}</span>
                                        <span class="label">Kembali</span>
                                        <span class="value num">{{ $fmtRp($struk['kembalian'] ?? 0) }}</span>
                                    @endif
                                </div>

                                <div class="totals-right">
                                    <span class="subtotal-label">Subtotal</span>
                                    <span class="subtotal-val num">{{ $fmtRp($struk['subtotal'] ?? 0) }}</span>

                                    @if (($struk['discount_item'] ?? 0) > 0)
                                        <span class="extra-label">Diskon Per Item</span>
                                        <span class="extra-val discount num">- {{ $fmtRp($struk['discount_item']) }}</span>
                                    @endif

                                    @if (($struk['discount_global'] ?? 0) > 0)
                                        <span class="extra-label">
                                            Diskon Global{{ !empty($struk['discount_percent']) && $struk['discount_percent'] > 0 ? ' (' . $struk['discount_percent'] . '%)' : '' }}
                                        </span>
                                        <span class="extra-val discount num">- {{ $fmtRp($struk['discount_global']) }}</span>
                                    @endif

                                    @if (($struk['voucher'] ?? 0) > 0)
                                        <span class="extra-label">Voucher</span>
                                        <span class="extra-val discount num">- {{ $fmtRp($struk['voucher']) }}</span>
                                    @endif

                                    @if (($struk['ppn_nominal'] ?? 0) > 0)
                                        <span class="extra-label">
                                            PPN{{ !empty($struk['ppn']) && $struk['ppn'] > 0 ? ' (' . $struk['ppn'] . '%)' : '' }}
                                        </span>
                                        <span class="extra-val num">+ {{ $fmtRp($struk['ppn_nominal']) }}</span>
                                    @endif

                                    <span class="grand-label">TOTAL</span>
                                    <span class="grand-val num">{{ $fmtRp($struk['total'] ?? 0) }}</span>
                                </div>
                            </div>
                        </section>
                    @endif
                </div>
            </article>
        @endforeach
    </main>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 150);
        });
    </script>
</body>
</html>
