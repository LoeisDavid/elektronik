<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Per Desa</title>
    
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

        /* SECTION: DESA TERLARIS */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #0D1C43;
            margin: 0 0 8px 0;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }

        .summary-card {
            border: 1px solid #94A3B8;
            border-radius: 10px;
            padding: 10px 14px 12px;
            background-color: #FFFFFF;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 10px;
            border-bottom: 1px dashed #E2E8F0;
            padding-bottom: 4px;
        }

        .card-header .unit {
            color: #475569;
            font-weight: 600;
        }

        .bar-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            font-size: 10.5px;
        }

        .bar-row:last-child {
            margin-bottom: 0;
        }

        .bar-label {
            width: 78px;
            font-weight: 500;
            color: #1E293B;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bar-track {
            flex: 1;
            background-color: #F1F5F9;
            border: 1px solid #CBD5E1;
            height: 9px;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }

        .bar-fill-blue {
            background-color: #1E3A8A;
            height: 100%;
            border-radius: 2px;
        }

        .bar-fill-green {
            background-color: #2E7D32;
            height: 100%;
            border-radius: 2px;
        }

        .bar-value {
            width: 44px;
            text-align: right;
            font-weight: 600;
            color: #0F172A;
        }

        /* SECTION: RINCIAN DESA */
        .table-container {
            border: 1px solid #94A3B8;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 40px;
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
            padding: 8px 12px;
            font-weight: 600;
            text-align: left;
            font-size: 11px;
        }

        table.detail-table th.text-center {
            text-align: center;
        }

        table.detail-table th.text-right {
            text-align: right;
        }

        table.detail-table tbody tr {
            border-bottom: 1px solid #E2E8F0;
        }

        table.detail-table tbody tr:last-child {
            border-bottom: none;
        }

        table.detail-table td {
            padding: 7px 12px;
            color: #1E293B;
        }

        table.detail-table td.text-center {
            text-align: center;
        }

        table.detail-table td.text-right {
            text-align: right;
            font-weight: 600;
        }

        .badge-nominal {
            background-color: #E8F5E9;
            border: 1px solid #A5D6A7;
            color: #2E7D32;
            font-size: 9px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 9999px;
            margin-left: 8px;
            display: inline-block;
        }

        .badge-frekuensi {
            background-color: #FFF3E0;
            border: 1px solid #FFCC80;
            color: #E65100;
            font-size: 9px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 9999px;
            margin-left: 8px;
            display: inline-block;
        }

        /* TANDA TANGAN */
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
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
            Preview Cetak — Laporan Penjualan Per Desa
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
                    <h1 class="main-title">Penjualan Per Desa</h1>
                </div>
                <div class="header-right">
                    <table>
                        <tr>
                            <td class="label">Tanggal</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $tanggal }}</td>
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

            {{-- SECTION: DESA TERLARIS --}}
            <h2 class="section-title">Desa Terlaris</h2>
            <div class="cards-grid">
                
                {{-- KARTU 1: TOP 5 NOMINAL PEMBELIAN --}}
                <div class="summary-card">
                    <div class="card-header">
                        <span>Top 5 Total Pembelian</span>
                        <span class="unit">Rp</span>
                    </div>
                    @forelse ($top5Nominal as $item)
                        @php
                            $pct = $maxNominal > 0 ? round(($item->total_nominal / $maxNominal) * 100) : 0;
                            // Format juta (jt)
                            $jt = $item->total_nominal >= 1000000 
                                ? number_format($item->total_nominal / 1000000, 1, ',', '.') . ' jt'
                                : number_format($item->total_nominal / 1000, 0, ',', '.') . ' rb';
                        @endphp
                        <div class="bar-row">
                            <div class="bar-label" title="{{ $item->DES }}">{{ $item->DES }}</div>
                            <div class="bar-track">
                                <div class="bar-fill-blue" style="width: {{ $pct }}%;"></div>
                            </div>
                            <div class="bar-value">{{ $jt }}</div>
                        </div>
                    @empty
                        <p style="font-size: 11px; color: #94A3B8; text-align: center; margin: 12px 0;">Belum ada data</p>
                    @endforelse
                </div>

                {{-- KARTU 2: TOP 5 FREKUENSI TRANSAKSI --}}
                <div class="summary-card">
                    <div class="card-header">
                        <span>Top 5 Total Pembelian</span>
                        <span class="unit">Kali</span>
                    </div>
                    @forelse ($top5Frekuensi as $item)
                        @php
                            $pct = $maxFrekuensi > 0 ? round(($item->total_transaksi / $maxFrekuensi) * 100) : 0;
                        @endphp
                        <div class="bar-row">
                            <div class="bar-label" title="{{ $item->DES }}">{{ $item->DES }}</div>
                            <div class="bar-track">
                                <div class="bar-fill-green" style="width: {{ $pct }}%;"></div>
                            </div>
                            <div class="bar-value">{{ $item->total_transaksi }}x</div>
                        </div>
                    @empty
                        <p style="font-size: 11px; color: #94A3B8; text-align: center; margin: 12px 0;">Belum ada data</p>
                    @endforelse
                </div>

            </div>

            {{-- SECTION: RINCIAN DESA --}}
            <h2 class="section-title">Rincian Desa</h2>
            <div class="table-container">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Nama Desa</th>
                            <th class="text-center" style="width: 100px;">Transaksi</th>
                            <th class="text-right" style="width: 160px;">Total Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($desaList as $index => $desa)
                            @php
                                $isTopNominal = ($desa->DS === $desaNominalTertinggi && $desa->total_nominal > 0);
                                $isTopFrekuensi = ($desa->DS === $desaFrekuensiTertinggi && $desa->total_transaksi > 0);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span>{{ $desa->DES }}</span>
                                    @if ($isTopNominal)
                                        <span class="badge-nominal">Nominal tertinggi</span>
                                    @endif
                                    @if ($isTopFrekuensi && !$isTopNominal)
                                        <span class="badge-frekuensi">Frekuensi tertinggi</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $desa->total_transaksi }}</td>
                                <td class="text-right">Rp {{ number_format($desa->total_nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: #94A3B8; padding: 20px;">
                                    Belum ada data penjualan per desa
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
