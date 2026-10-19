<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Cek Stok</title>
    
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

        /* Top bar untuk tampilan layar */
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
        }

        /* Header Laporan */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .header-title-wrap .sub-title {
            font-size: 12px;
            color: #475569;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .header-title-wrap .main-title {
            font-size: 24px;
            font-weight: 700;
            color: #0D1C43;
            line-height: 1.2;
        }

        .meta-table {
            font-size: 11px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .meta-table td.label {
            color: #475569;
            font-weight: 500;
            width: 75px;
        }

        .meta-table td.val {
            color: #0F172A;
            font-weight: 600;
            padding-left: 12px;
        }

        /* Garis Pemisah Biru */
        .divider {
            width: 100%;
            height: 1.5px;
            background-color: #3B5284;
            opacity: 0.6;
            margin: 10px 0 16px 0;
        }

        /* Section Title */
        .section-heading {
            font-size: 13px;
            font-weight: 700;
            color: #0F172A;
            margin: 14px 0 10px 0;
        }

        /* Ringkasan 4 Kartu */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .summary-card {
            border-radius: 8px;
            padding: 10px 14px 12px;
            text-align: left;
        }

        .summary-card .card-label {
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .summary-card .card-number {
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            padding: 4px 0;
        }

        /* Tema Tiap Kartu */
        .card-total {
            background-color: #0D1C43;
            color: #ffffff;
            border: 1px solid #0D1C43;
        }
        .card-total .card-label {
            color: #ffffff;
        }
        .card-total .card-number {
            color: #ffffff;
        }

        .card-terjual {
            background-color: #E8F8EA;
            border: 1px solid #86EFAC;
        }
        .card-terjual .card-label {
            color: #166534;
        }
        .card-terjual .card-number {
            color: #16A34A;
        }

        .card-belum {
            background-color: #FFFBEB;
            border: 1px solid #FDE047;
        }
        .card-belum .card-label {
            color: #CA8A04;
        }
        .card-belum .card-number {
            color: #EAB308;
        }

        .card-checking {
            background-color: #F3F4F6;
            border: 1px solid #D1D5DB;
        }
        .card-checking .card-label {
            color: #4B5563;
        }
        .card-checking .card-number {
            color: #6B7280;
        }

        /* Tabel Rincian Barang */
        .table-outer {
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table.detail-table thead {
            display: table-header-group;
        }

        table.detail-table th {
            background-color: #0D1C43 !important;
            color: #ffffff !important;
            font-weight: 600;
            padding: 9px 8px;
            text-align: center;
            border: none;
        }

        table.detail-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
            border-bottom: 1px solid #E2E8F0;
        }

        table.detail-table tbody tr:last-child {
            border-bottom: none;
        }

        table.detail-table td {
            padding: 7px 10px;
            vertical-align: middle;
            color: #1E293B;
        }

        .td-no {
            text-align: center;
            width: 7%;
        }

        .td-barcode {
            text-align: center;
            font-family: inherit;
            width: 33%;
        }

        .td-nama {
            text-align: center;
            width: 38%;
        }

        .td-status {
            text-align: center;
            width: 22%;
        }

        /* Pill Badge Status */
        .status-pill {
            display: inline-block;
            min-width: 86px;
            padding: 2.5px 12px;
            font-size: 10px;
            font-weight: 500;
            border-radius: 4px;
            text-align: center;
        }

        .status-checking {
            border: 1px solid #9CA3AF;
            background-color: #ffffff;
            color: #4B5563;
        }

        .status-sudah_terjual {
            border: 1px solid #86EFAC;
            background-color: #E8F8EA;
            color: #166534;
        }

        .status-belum_terjual {
            border: 1px solid #FDE047;
            background-color: #FFFBEB;
            color: #CA8A04;
        }

        /* Bagian Tanda Tangan */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 45px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .sig-block {
            width: 230px;
        }

        .sig-line {
            width: 100%;
            border-bottom: 1px solid #475569;
            height: 65px; /* Tempat tanda tangan */
            margin-bottom: 8px;
        }

        .sig-text {
            font-size: 11px;
            color: #1E293B;
            font-weight: 500;
        }

        /* Print Media Styles */
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .page-wrapper {
                padding: 0 !important;
                display: block !important;
            }

            .paper {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            table.detail-table thead {
                display: table-header-group !important;
            }

            table.detail-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .signature-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Layar (Disembunyikan saat dicetak) -->
    <div class="no-print-bar">
        <div class="title">
            Preview Cetak: Laporan Cek Stok
        </div>
        <div class="btn-group">
            <button type="button" onclick="window.close()" class="btn-close">
                ← Tutup
            </button>
            <button type="button" onclick="window.print()" class="btn-print">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Laporan
            </button>
        </div>
    </div>

    <!-- Lembar Kertas Cetak -->
    <div class="page-wrapper">
        <div class="paper">

            <!-- Header Laporan -->
            <div class="header-section">
                <div class="header-title-wrap">
                    <div class="sub-title">Laporan</div>
                    <div class="main-title">Cek Stok</div>
                </div>
                <div>
                    <table class="meta-table">
                        <tr>
                            <td class="label">Tanggal</td>
                            <td class="val">{{ $tanggal }}</td>
                        </tr>
                        <tr>
                            <td class="label">Petugas</td>
                            <td class="val">{{ $petugas }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Garis Pemisah -->
            <div class="divider"></div>

            <!-- Ringkasan -->
            <div class="section-heading">Ringkasan</div>
            <div class="summary-grid">
                <div class="summary-card card-total">
                    <div class="card-label">Total Data</div>
                    <div class="card-number">{{ $summary['total'] ?? 0 }}</div>
                </div>
                <div class="summary-card card-terjual">
                    <div class="card-label">Sudah Terjual</div>
                    <div class="card-number">{{ $summary['sudah_terjual'] ?? 0 }}</div>
                </div>
                <div class="summary-card card-belum">
                    <div class="card-label">Belum Terjual</div>
                    <div class="card-number">{{ $summary['belum_terjual'] ?? 0 }}</div>
                </div>
                <div class="summary-card card-checking">
                    <div class="card-label">Tidak Terscan</div>
                    <div class="card-number">{{ $summary['tidak_terscan'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Rincian Barang -->
            <div class="section-heading">Rincian Barang</div>
            <div class="table-outer">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th class="td-no">No</th>
                            <th class="td-barcode">Kode Barcode</th>
                            <th class="td-nama">Nama Barang</th>
                            <th class="td-status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $idx => $row)
                            @php
                                $status = $row['status'] ?? 'checking';
                                $statusLabel = match($status) {
                                    'sudah_terjual' => 'Sudah Terjual',
                                    'belum_terjual' => 'Belum Terjual',
                                    default         => 'Checking',
                                };
                            @endphp
                            <tr>
                                <td class="td-no">{{ $idx + 1 }}</td>
                                <td class="td-barcode">{{ $row['kode_barcode'] }}</td>
                                <td class="td-nama">{{ $row['nama_barang'] }}</td>
                                <td class="td-status">
                                    <span class="status-pill status-{{ $status }}">{{ $statusLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 20px; color: #64748B;">Tidak ada data barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tanda Tangan -->
            <div class="signature-section">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-text">Dibuat oleh ({{ $petugas }})</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-text">Diperiksa oleh</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Otomatis buka dialog print saat halaman selesai dimuat
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
