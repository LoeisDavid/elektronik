<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nota Penjualan - {{ $struk['ref_no'] }}</title>

    @include('POS.partials.nota-styles')

    <style>
        @page {
            size: legal;
            margin: 0;
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
            flex-wrap: wrap;
            align-items: stretch;
            justify-content: space-between;
            gap: 12px;
        }

        .print-guide {
            flex: 1 1 0;
            min-width: 280px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 12px;
            line-height: 1.5;
            color: #1e293b;
        }

        .print-guide-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .print-guide ul {
            margin: 0;
            padding-left: 18px;
        }

        .toolbar-actions {
            display: flex;
            flex-direction: column;
            justify-content: stretch;
            gap: 8px;
            min-width: 180px;
        }

        .btn-toolbar {
            appearance: none;
            width: 100%;
            flex: 1 1 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #9ca3af;
            background: #ffffff;
            color: #111827;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            padding: 11px 22px;
            border-radius: 12px;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-toolbar:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .btn-toolbar:active {
            background: #e5e7eb;
        }

        .btn-toolbar.primary {
            background: #1A1F36;
            border-color: #1A1F36;
            color: #ffffff;
        }

        .btn-toolbar.primary:hover,
        .btn-toolbar.primary:active {
            background: #000000;
            border-color: #000000;
            color: #ffffff;
        }

        /* Screen sheet slot layout (Legal 215 x 355 mm) */
        #receipt-root.nota-root .sheet-slot {
            width: 215mm;
            height: 355mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        }

        #receipt-root.nota-root .sheet {
            box-shadow: none;
            background: transparent;
        }

        #receipt-root.nota-root .folio-cut-guide {
            display: block;
            position: absolute;
            left: 0;
            right: 0;
            top: 110mm;
            border-top: 1px dashed #94a3b8;
            pointer-events: none;
        }

        #receipt-root.nota-root .folio-cut-guide span {
            position: absolute;
            right: 6mm;
            top: 2mm;
            font-size: 9px;
            color: #64748b;
            letter-spacing: 0.4px;
        }

        @media print {
            html, body {
                background: #ffffff;
                padding: 0;
                margin: 0;
                overflow: visible;
            }

            .print-bar,
            .folio-cut-guide,
            #receipt-root.nota-root .preprinted-guide {
                display: none !important;
            }

            #receipt-root.nota-root {
                display: block;
                min-width: 0 !important;
                padding: 0;
                gap: 0;
            }

            #receipt-root.nota-root .sheet-slot {
                box-shadow: none !important;
                background: transparent !important;
                overflow: hidden !important;
                margin: 0 !important;
                padding: 20mm 0 0 0 !important;
                width: 209mm;
                height: 129mm;
            }

            #receipt-root.nota-root .sheet-slot + .sheet-slot {
                break-before: page;
                page-break-before: always;
            }

            #receipt-root.nota-root .sheet {
                width: 215mm;
                height: 110mm;
                padding: 5mm;
                margin: 0;
                box-shadow: none;
                background: transparent;
                overflow: hidden;
            }

            #receipt-root.nota-root .sheet-inner {
                padding: 20mm 32mm 0 9mm;
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
        $fmtPct = function ($val): string {
            $num = round((float) $val, 2);
            if ($num <= 0) {
                return '-';
            }
            return rtrim(rtrim(number_format($num, 2, ',', '.'), '0'), ',') . '%';
        };
    @endphp

    <div class="print-bar">
        <div class="print-guide" role="note" aria-label="Panduan Cetak">
            <div class="print-guide-title">Panduan Print:</div>
            <ul>
                <li>Perhatikan setting dan preview sebelum mengeprint</li>
                <li>Pilihan ukuran kertas harus legal (215 x 355mm)</li>
                <li>Margin harus Default / None (margin atas 20mm sudah diatur otomatis oleh sistem)</li>
                <li>Scale default</li>
            </ul>
        </div>

        <div class="toolbar-actions">
            <button class="btn-toolbar primary" type="button" onclick="window.print()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            <button class="btn-toolbar" type="button" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <main id="receipt-root" class="nota-root" aria-label="Nota Penjualan">
        @foreach ($pages as $pageIndex => $pageItems)
            @php
                $isLastPage = ($pageIndex === $totalPages - 1);
                $startIndex = $pageIndex * $rowsPerPage;
                $hasExtraTotals = $isLastPage && (
                    ($struk['discount_global'] ?? 0) > 0 ||
                    ($struk['voucher'] ?? 0) > 0 ||
                    ($struk['ppn_nominal'] ?? 0) > 0
                );
                $displaySubtotal = max(0, ($struk['subtotal'] ?? 0) - ($struk['discount_item'] ?? 0));
            @endphp

            <div class="sheet-slot">
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
                                        <th class="col-price num">Harga Satuan</th>
                                        <th class="col-disc center">Diskon</th>
                                        <th class="col-voucher num">Voucher</th>
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
                                                <td class="center">&nbsp;</td>
                                                <td class="num">&nbsp;</td>
                                                <td class="num">&nbsp;</td>
                                            </tr>
                                        @else
                                            @php
                                                $qty = (int) ($item['qty'] ?? 1);
                                                $unitPrice = (float) ($item['price'] ?? 0);
                                                $discPct = (float) ($item['discount'] ?? 0);
                                                $discVoucher = (float) ($item['discount_voucher_total'] ?? $item['discount_voucher'] ?? 0);
                                                $lineTotal = $item['subtotal'] !== null ? (float) $item['subtotal'] : ($qty * $unitPrice);
                                            @endphp
                                            <tr>
                                                <td class="center">{{ $startIndex + $rowOffset + 1 }}</td>
                                                <td title="{{ $item['nama'] }}">
                                                    {{ $item['nama'] }}
                                                </td>
                                                <td class="center">{{ $qty }}</td>
                                                <td class="num">{{ $fmtRp($unitPrice) }}</td>
                                                <td class="center">{{ $fmtPct($discPct) }}</td>
                                                <td class="num">{{ $discVoucher > 0 ? $fmtRp($discVoucher) : '-' }}</td>
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
                                        <span class="subtotal-val num">{{ $fmtRp($displaySubtotal) }}</span>

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
                <div class="folio-cut-guide" aria-hidden="true">
                    <span>Batas 1/3 Folio (110 mm)</span>
                </div>
            </div>
        @endforeach
    </main>
</body>
</html>
