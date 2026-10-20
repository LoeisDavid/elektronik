<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan — Elektronik</title>
    
    <!-- Google Fonts: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #525659;
            color: #1E293B;
            -webkit-font-smoothing: antialiased;
        }

        /* Top bar untuk preview di layar */
        .no-print-bar {
            position: sticky;
            top: 0;
            z-index: 50;
            background-color: #0D1C43;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }

        .no-print-bar .title {
            font-size: 14px;
            font-weight: 600;
        }

        .no-print-bar .btn-group {
            display: flex;
            gap: 10px;
        }

        .no-print-bar button, .no-print-bar a {
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-close {
            background-color: transparent;
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.4);
        }
        .btn-close:hover {
            background-color: rgba(255,255,255,0.1);
            border-color: #ffffff;
        }

        .btn-print {
            background-color: #F2B80C;
            color: #0D1C43;
            border: none;
        }
        .btn-print:hover {
            background-color: #e0aa0a;
        }

        /* Container lembar kertas */
        .page-wrapper {
            padding: 24px 0 48px;
            display: flex;
            justify-content: center;
        }

        .paper {
            background-color: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 18mm 18mm 20mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            border-radius: 2px;
            position: relative;
            box-sizing: border-box;
        }

        /* HEADER */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .header-left .sub-title {
            font-size: 11px;
            color: #64748B;
            font-weight: 500;
            margin: 0 0 2px 0;
            letter-spacing: 0.3px;
        }

        .header-left .main-title {
            font-size: 22px;
            font-weight: 700;
            color: #0D1C43;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .header-right table {
            border-collapse: collapse;
            font-size: 11px;
            color: #1E293B;
        }

        .header-right td {
            padding: 1.5px 4px;
        }

        .header-right td.label {
            color: #475569;
            font-weight: 500;
            width: 60px;
        }

        .header-right td.colon {
            width: 8px;
            text-align: center;
        }

        .header-right td.val {
            font-weight: 600;
            color: #0F172A;
            text-align: right;
        }

        .divider-line {
            height: 1.5px;
            background-color: #0D1C43;
            width: 100%;
            margin: 8px 0 16px 0;
        }

        /* SECTION: RINCIAN PENJUALAN */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #0D1C43;
            margin: 0 0 10px 0;
        }

        .table-container {
            border: 1px solid #94A3B8;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table.detail-table thead {
            background-color: #0D1C43;
            color: #FFFFFF;
        }

        table.detail-table th {
            padding: 8px 10px;
            font-weight: 600;
            font-size: 11px;
        }

        table.detail-table th.text-left { text-align: left; }
        table.detail-table th.text-center { text-align: center; }
        table.detail-table th.text-right { text-align: right; }

        /* INVOICE HEADER ROW */
        tr.invoice-header-row td {
            background-color: #F8FAFC;
            border-top: 1px solid #CBD5E1;
            border-bottom: 1px solid #CBD5E1;
            padding: 8px 12px;
            font-size: 11px;
        }

        .inv-head-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .inv-head-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .inv-head-code {
            font-weight: 700;
            color: #0F172A;
        }

        .inv-head-date {
            color: #475569;
        }

        .inv-head-cst {
            font-weight: 600;
            color: #1E293B;
        }

        .inv-head-desa {
            color: #334155;
            font-weight: 500;
        }

        .inv-head-right {
            font-weight: 500;
            color: #334155;
        }

        /* ITEM ROWS */
        tr.item-row td {
            padding: 6px 10px;
            color: #1E293B;
            font-size: 11px;
            border-bottom: 1px solid #F1F5F9;
        }

        tr.item-row:last-of-type td {
            border-bottom: none;
        }

        /* SUMMARY BREAKDOWN ROWS */
        tr.summary-row td {
            padding: 2.5px 10px;
            font-size: 10.5px;
            color: #475569;
        }

        tr.summary-row td.sub-label {
            text-align: right;
            color: #475569;
            font-weight: 500;
        }

        tr.summary-row td.sub-val {
            text-align: right;
            color: #1E293B;
            font-weight: 500;
        }

        /* INVOICE TOTAL ROW */
        tr.invoice-total-row td {
            padding: 7px 10px;
            border-top: 1.5px solid #64748B;
            border-bottom: 2px solid #0D1C43;
            background-color: #FFFFFF;
            font-size: 11px;
            font-weight: 700;
        }

        tr.invoice-total-row td.tot-label {
            text-align: right;
            color: #0F172A;
            padding-right: 14px;
        }

        tr.invoice-total-row td.tot-val {
            text-align: right;
            color: #0F172A;
        }

        /* TANDA TANGAN */
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 0 10px;
        }

        .signature-box {
            width: 170px;
            text-align: left;
        }

        .signature-line {
            border-top: 1.5px solid #64748B;
            width: 100%;
            margin-bottom: 6px;
        }

        .signature-name {
            font-size: 11px;
            color: #334155;
            font-weight: 500;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print-bar {
                display: none !important;
            }
            .page-wrapper {
                padding: 0;
            }
            .paper {
                width: 100%;
                min-height: auto;
                box-shadow: none;
                border-radius: 0;
                padding: 15mm 15mm;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    {{-- NAV BAR ATAS (HANYA DITAMPILKAN DI LAYAR) --}}
    <div class="no-print-bar">
        <div class="title">
            Preview Cetak — Laporan Penjualan
        </div>
        <div class="btn-group">
            <button type="button" class="btn-close" onclick="window.close(); if(!window.closed){ window.history.back(); }">
                Tutup
            </button>
            <button type="button" class="btn-print" onclick="window.print()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                </svg>
                <span>Cetak Dokumen</span>
            </button>
        </div>
    </div>

    {{-- KERTAS DOKUMEN CETAK A4 --}}
    <div class="page-wrapper">
        <div class="paper">
            
            {{-- HEADER LAPORAN --}}
            <div class="header-container">
                <div class="header-left">
                    <p class="sub-title">Laporan</p>
                    <h1 class="main-title">Penjualan Penjualan</h1>
                </div>
                <div class="header-right">
                    <table>
                        <tr>
                            <td class="label">Tanggal</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $tanggalDisplay }}</td>
                        </tr>
                        <tr>
                            <td class="label">Petugas</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $petugas }}</td>
                        </tr>
                        <tr>
                            <td class="label">Lokasi</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $lokasi }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="divider-line"></div>

            {{-- SECTION: RINCIAN PENJUALAN --}}
            <h2 class="section-title">Rincian Penjualan</h2>
            <div class="table-container">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th class="text-left" style="width: 105px;">Kode Barang</th>
                            <th class="text-left">Nama Barang</th>
                            <th class="text-right" style="width: 95px;">Harga Satuan</th>
                            <th class="text-center" style="width: 80px;">Diskon Satuan</th>
                            <th class="text-right" style="width: 95px;">Voucher Satuan</th>
                            <th class="text-right" style="width: 100px;">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reportData as $inv)
                            {{-- SUBHEADER BARIS INVOICE --}}
                            <tr class="invoice-header-row">
                                <td colspan="6">
                                    <div class="inv-head-flex">
                                        <div class="inv-head-left">
                                            <span class="inv-head-code">{{ $inv['ref_no'] }}</span>
                                            <span class="inv-head-date">{{ $inv['waktu'] }}</span>
                                            <span class="inv-head-cst">{{ $inv['customer'] }}</span>
                                            <span class="inv-head-desa">{{ $inv['desa'] }}</span>
                                        </div>
                                        <div class="inv-head-right">
                                            {{ $inv['kasir_metode'] }}
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- BARIS ITEM-ITEM --}}
                            @foreach ($inv['items'] as $item)
                                <tr class="item-row">
                                    <td class="font-mono">{{ $item['kode'] }}</td>
                                    <td>{{ $item['nama'] }}</td>
                                    <td class="text-right">{{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item['diskon_persen'] > 0 ? $item['diskon_persen'] . '%' : '0%' }}</td>
                                    <td class="text-right">{{ $item['voucher_satuan'] > 0 ? '-' . number_format($item['voucher_satuan'], 0, ',', '.') : '0' }}</td>
                                    <td class="text-right font-medium">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            {{-- BREAKDOWN SUMMARY --}}
                            <tr class="summary-row" style="padding-top: 4px;">
                                <td colspan="4"></td>
                                <td class="sub-label">Sub Total</td>
                                <td class="sub-val">{{ number_format($inv['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                            <tr class="summary-row">
                                <td colspan="4"></td>
                                <td class="sub-label">Diskon Global</td>
                                <td class="sub-val">{{ $inv['diskon_global'] > 0 ? '-' . number_format($inv['diskon_global'], 0, ',', '.') : '0' }}</td>
                            </tr>
                            <tr class="summary-row">
                                <td colspan="4"></td>
                                <td class="sub-label">Voucher Global</td>
                                <td class="sub-val">{{ $inv['voucher_global'] > 0 ? '-' . number_format($inv['voucher_global'], 0, ',', '.') : '0' }}</td>
                            </tr>
                            <tr class="summary-row">
                                <td colspan="4"></td>
                                <td class="sub-label">PPN ({{ $inv['ppn_pct'] }}%)</td>
                                <td class="sub-val">{{ number_format($inv['ppn_nominal'], 0, ',', '.') }}</td>
                            </tr>

                            {{-- TOTAL INVOICE --}}
                            <tr class="invoice-total-row">
                                <td colspan="5" class="tot-label">
                                    Total {{ $inv['ref_no'] }}
                                </td>
                                <td class="tot-val">
                                    Rp {{ number_format($inv['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94A3B8; padding: 24px;">
                                    Tidak ada data transaksi pada rentang tanggal yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TANDA TANGAN --}}
            <div class="signature-container">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Dibuat oleh ({{ $petugas }})</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Diperiksa oleh</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
