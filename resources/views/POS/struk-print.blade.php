<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nota Penjualan - {{ $struk['ref_no'] }}</title>

    @include('POS.partials.nota-styles')

    <style id="notaPageStyle">
        @page {
            size: 215mm 110mm;
            margin: 0;
        }
    </style>

    <style>
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
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .paper-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .paper-group {
            display: inline-flex;
            background: #e2e8f0;
            padding: 3px;
            border-radius: 6px;
            gap: 3px;
        }

        .btn-paper {
            appearance: none;
            border: none;
            background: transparent;
            color: #334155;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 11px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
        }

        .btn-paper:hover {
            color: #0f172a;
        }

        .btn-paper.active {
            background: #111111;
            color: #ffffff;
        }

        .btn-flip,
        .btn-toggle {
            appearance: none;
            border: 1px solid #94a3b8;
            background: #ffffff;
            color: #1e293b;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-flip {
            display: none;
        }

        .btn-flip.visible {
            display: inline-block;
        }

        .btn-flip.active,
        .btn-toggle.active {
            background: #1e293b;
            color: #ffffff;
            border-color: #1e293b;
        }

        .paper-hint {
            width: 100%;
            font-size: 11.5px;
            color: #475569;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
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

        .btn-toolbar.primary {
            background: #111111;
            color: #ffffff;
        }

        .btn-toolbar.primary:hover {
            background: #333333;
        }

        /* Screen sheet slot layouts */
        #receipt-root.nota-root[data-paper="landscape"] .sheet-slot {
            width: 215mm;
            height: 110mm;
            position: relative;
        }

        #receipt-root.nota-root[data-paper="portrait"] {
            min-width: 110mm;
        }

        #receipt-root.nota-root[data-paper="portrait"] .sheet-slot {
            width: 110mm;
            height: 215mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        }

        #receipt-root.nota-root[data-paper="portrait"] .sheet {
            position: absolute;
            top: 0;
            left: 0;
            box-shadow: none;
            transform-origin: top left;
            transform: translateY(215mm) rotate(-90deg);
        }

        #receipt-root.nota-root[data-paper="portrait"][data-flip="1"] .sheet {
            transform: translateX(110mm) rotate(90deg);
        }

        #receipt-root.nota-root[data-paper="folio"] .sheet-slot {
            width: 215mm;
            height: 330mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        }

        #receipt-root.nota-root[data-paper="folio"] .sheet {
            box-shadow: none;
            background: transparent;
        }

        .folio-cut-guide {
            display: none;
        }

        #receipt-root.nota-root[data-paper="folio"] .folio-cut-guide {
            display: block;
            position: absolute;
            left: 0;
            right: 0;
            top: 110mm;
            border-top: 1px dashed #94a3b8;
            pointer-events: none;
        }

        #receipt-root.nota-root[data-paper="folio"] .folio-cut-guide span {
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
            .folio-cut-guide {
                display: none !important;
            }

            #receipt-root.nota-root:not([data-print-preprinted="1"]) .preprinted-guide {
                display: none !important;
            }

            #receipt-root.nota-root[data-print-preprinted="1"] .preprinted-guide {
                display: block !important;
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
            }

            #receipt-root.nota-root[data-paper="landscape"] .sheet-slot {
                width: 209mm;
                height: 109mm;
            }

            #receipt-root.nota-root[data-paper="portrait"] .sheet-slot {
                width: 109mm;
                height: 214mm;
            }

            #receipt-root.nota-root[data-paper="folio"] .sheet-slot {
                width: 209mm;
                height: 109mm;
            }

            #receipt-root.nota-root[data-print-preprinted="1"][data-paper="landscape"] .sheet-slot,
            #receipt-root.nota-root[data-print-preprinted="1"][data-paper="folio"] .sheet-slot {
                width: 214mm;
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
        <div class="paper-controls">
            <div class="paper-group" role="group" aria-label="Posisi Kertas">
                <button type="button" class="btn-paper active" data-paper-option="landscape" onclick="setPaperMode('landscape')">
                    Landscape paper position
                </button>
                <button type="button" class="btn-paper" data-paper-option="portrait" onclick="setPaperMode('portrait')">
                    Portrait paper position
                </button>
                <button type="button" class="btn-paper" data-paper-option="folio" onclick="setPaperMode('folio')">
                    Folio test (215x330)
                </button>
            </div>
            <button type="button" id="btnFlipPortrait" class="btn-flip" onclick="togglePortraitFlip()" title="Putar 180 derajat sesuai arah masuk kertas">
                Flip 180°
            </button>
            <button type="button" id="btnPrintPreprinted" class="btn-toggle" aria-pressed="false" onclick="togglePrintPreprinted()" title="Ikut cetak header Toko Bogor, watermark, dan garis PERHATIAN">
                Print pre-printed text
            </button>
            <div id="paperModeHint" class="paper-hint">
                Edge first (215 x 110 mm). Pilih kertas 215 x 110 mm di pengaturan printer.
            </div>
        </div>

        <div class="toolbar-actions">
            <button class="btn-toolbar" type="button" onclick="window.close()">Tutup</button>
            <button class="btn-toolbar primary" type="button" onclick="window.print()">Print</button>
        </div>
    </div>

    <main id="receipt-root" class="nota-root" data-paper="landscape" data-flip="0" data-print-preprinted="0" aria-label="Nota Penjualan">
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

    <script>
        const STORAGE_PAPER_KEY = 'nota_paper_position';
        const STORAGE_FLIP_KEY = 'nota_portrait_flip';
        const STORAGE_PREPRINTED_KEY = 'nota_print_preprinted';

        const PAPER_MODES = {
            landscape: {
                pageSize: '215mm 110mm',
                hint: 'Edge first (215 x 110 mm). Pilih kertas 215 x 110 mm di printer.',
            },
            portrait: {
                pageSize: '110mm 215mm',
                hint: 'Sideways (110 x 215 mm). Buat User Defined 110 x 215 mm di printer, sesuaikan arah header TOKO BOGOR.',
            },
            folio: {
                pageSize: '215mm 330mm',
                hint: 'Folio test (215 x 330 mm). Gunakan Brother MFC-J3530DW Printer -> More settings -> Paper size: Folio / Legal, Scale: 100% (atau Ctrl+Shift+P).',
            },
        };

        function readStorage(key, fallback) {
            try {
                return localStorage.getItem(key) ?? fallback;
            } catch (e) {
                return fallback;
            }
        }

        function writeStorage(key, value) {
            try {
                localStorage.setItem(key, value);
            } catch (e) {}
        }

        function applyPaperState(mode, flip, printPreprinted) {
            const safeMode = PAPER_MODES[mode] ? mode : 'landscape';
            const safeFlip = flip === '1' ? '1' : '0';
            const safePreprinted = printPreprinted === '1' ? '1' : '0';
            const root = document.getElementById('receipt-root');
            const pageStyle = document.getElementById('notaPageStyle');
            const hintEl = document.getElementById('paperModeHint');
            const flipBtn = document.getElementById('btnFlipPortrait');
            const preprintedBtn = document.getElementById('btnPrintPreprinted');

            if (root) {
                root.setAttribute('data-paper', safeMode);
                root.setAttribute('data-flip', safeFlip);
                root.setAttribute('data-print-preprinted', safePreprinted);
            }

            if (pageStyle) {
                pageStyle.textContent = `@page { size: ${PAPER_MODES[safeMode].pageSize}; margin: 0; }`;
            }

            if (hintEl) {
                hintEl.textContent = PAPER_MODES[safeMode].hint;
            }

            document.querySelectorAll('[data-paper-option]').forEach((btn) => {
                btn.classList.toggle('active', btn.dataset.paperOption === safeMode);
            });

            if (flipBtn) {
                flipBtn.classList.toggle('visible', safeMode === 'portrait');
                flipBtn.classList.toggle('active', safeMode === 'portrait' && safeFlip === '1');
            }

            if (preprintedBtn) {
                preprintedBtn.classList.toggle('active', safePreprinted === '1');
                preprintedBtn.setAttribute('aria-pressed', safePreprinted === '1' ? 'true' : 'false');
            }
        }

        window.setPaperMode = function setPaperMode(mode) {
            const safeMode = PAPER_MODES[mode] ? mode : 'landscape';
            writeStorage(STORAGE_PAPER_KEY, safeMode);
            applyPaperState(safeMode, readStorage(STORAGE_FLIP_KEY, '0'), readStorage(STORAGE_PREPRINTED_KEY, '0'));
        };

        window.togglePortraitFlip = function togglePortraitFlip() {
            const root = document.getElementById('receipt-root');
            const nextFlip = root?.getAttribute('data-flip') === '1' ? '0' : '1';
            writeStorage(STORAGE_FLIP_KEY, nextFlip);
            applyPaperState(root?.getAttribute('data-paper') || 'portrait', nextFlip, readStorage(STORAGE_PREPRINTED_KEY, '0'));
        };

        window.togglePrintPreprinted = function togglePrintPreprinted() {
            const root = document.getElementById('receipt-root');
            const nextVal = root?.getAttribute('data-print-preprinted') === '1' ? '0' : '1';
            writeStorage(STORAGE_PREPRINTED_KEY, nextVal);
            applyPaperState(
                root?.getAttribute('data-paper') || readStorage(STORAGE_PAPER_KEY, 'landscape'),
                root?.getAttribute('data-flip') || readStorage(STORAGE_FLIP_KEY, '0'),
                nextVal
            );
        };

        (function initPaperMode() {
            applyPaperState(
                readStorage(STORAGE_PAPER_KEY, 'landscape'),
                readStorage(STORAGE_FLIP_KEY, '0'),
                readStorage(STORAGE_PREPRINTED_KEY, '0')
            );
        })();

        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 150);
        });
    </script>
</body>
</html>
