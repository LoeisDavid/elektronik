<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perpindahan Barang — {{ $rp->RP }}</title>
    
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

        /* Container lembar kertas A4 */
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

        .header-left .doc-number {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-top: 3px;
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
            width: 75px;
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

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-request { background: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }
        .badge-pengiriman { background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D; }
        .badge-diterima { background: #DCFCE7; color: #15803D; border: 1px solid #86EFAC; }
        .badge-ditolak { background: #FEE2E2; color: #B91C1C; border: 1px solid #FCA5A5; }

        .divider-line {
            height: 1.5px;
            background-color: #0D1C43;
            width: 100%;
            margin: 8px 0 16px 0;
        }

        /* INFO BOX */
        .info-box {
            border: 1px solid #94A3B8;
            border-radius: 8px;
            padding: 10px 14px;
            background-color: #F8FAFC;
            margin-bottom: 18px;
            font-size: 11px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .info-group h4 {
            margin: 0 0 6px 0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748B;
            font-weight: 700;
        }

        .info-group table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-group td {
            padding: 2px 0;
        }

        .info-group td.lbl {
            color: #475569;
            width: 90px;
        }

        .info-group td.val {
            font-weight: 600;
            color: #0F172A;
        }

        /* TABLE */
        .table-title {
            font-size: 13px;
            font-weight: 700;
            color: #0D1C43;
            margin: 0 0 8px 0;
        }

        .table-container {
            border: 1px solid #94A3B8;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
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
            padding: 7px 10px;
            font-weight: 600;
            text-align: left;
            font-size: 11px;
        }

        table.detail-table th.text-center { text-align: center; }
        table.detail-table th.text-right { text-align: right; }

        table.detail-table tbody tr {
            border-bottom: 1px solid #E2E8F0;
        }

        table.detail-table tbody tr:last-child {
            border-bottom: none;
        }

        table.detail-table td {
            padding: 6px 10px;
            color: #1E293B;
        }

        table.detail-table td.text-center { text-align: center; }
        table.detail-table td.text-right { text-align: right; }

        /* ITEM STATUS PILL */
        .item-st {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }

        .item-st-ready { background: #EFF6FF; color: #1D4ED8; }
        .item-st-dikirim { background: #FEF3C7; color: #B45309; }
        .item-st-diterima { background: #DCFCE7; color: #15803D; }
        .item-st-rusak { background: #FFEDD5; color: #C2410C; }
        .item-st-hilang { background: #FEE2E2; color: #B91C1C; }
        .item-st-ditolak { background: #F3F4F6; color: #4B5563; }

        /* SUMMARY ROW */
        .summary-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 11px;
            margin-bottom: 30px;
        }

        .summary-left {
            font-weight: 700;
            color: #0F172A;
        }

        .summary-right {
            font-weight: 500;
            color: #475569;
        }

        /* TANDA TANGAN (3 PIHAK) */
        .signature-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            margin-top: 36px;
            padding: 0 10px;
            text-align: center;
        }

        .signature-box .role {
            font-size: 11px;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 50px;
        }

        .signature-line {
            border-top: 1.5px solid #64748B;
            width: 80%;
            margin: 0 auto 6px auto;
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
                padding: 12mm 15mm;
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
            Preview Cetak — Laporan Perpindahan Stok ({{ $rp->RP }})
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
                    <h1 class="main-title">Perpindahan Stok Barang</h1>
                    <p class="doc-number">No. Dokumen: <strong>{{ $rp->RP }}</strong></p>
                </div>
                <div class="header-right">
                    <table>
                        <tr>
                            <td class="label">Status</td>
                            <td class="colon">:</td>
                            <td class="val">
                                @php
                                    $stClass = match($rp->SS) {
                                        'Request'    => 'badge-request',
                                        'Pengiriman' => 'badge-pengiriman',
                                        'Diterima'   => 'badge-diterima',
                                        'Ditolak'    => 'badge-ditolak',
                                        default      => 'badge-request',
                                    };
                                @endphp
                                <span class="status-badge {{ $stClass }}">{{ $rp->SS }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Cetak</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $tanggalCetak }}</td>
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

            {{-- BOX INFORMASI PERPINDAHAN --}}
            <div class="info-box">
                <div class="info-grid">
                    
                    {{-- KOLOM 1: GUDANG --}}
                    <div class="info-group">
                        <h4>Rute Perpindahan</h4>
                        <table>
                            <tr>
                                <td class="lbl">Gudang Asal</td>
                                <td class="val">{{ $rp->gudangAsal?->DES ?? $rp->GD_ASAL }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Gudang Tujuan</td>
                                <td class="val">{{ $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- KOLOM 2: TANGGAL --}}
                    <div class="info-group">
                        <h4>Riwayat Tanggal</h4>
                        <table>
                            <tr>
                                <td class="lbl">Tgl Request</td>
                                <td class="val">{{ $rp->DTRQ ? \Carbon\Carbon::parse($rp->DTRQ)->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Tgl Pengiriman</td>
                                <td class="val">{{ $rp->DTCF1 ? \Carbon\Carbon::parse($rp->DTCF1)->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Tgl Diterima</td>
                                <td class="val">{{ $rp->DTCF2 ? \Carbon\Carbon::parse($rp->DTCF2)->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- KOLOM 3: PETUGAS --}}
                    <div class="info-group">
                        <h4>Penanggung Jawab</h4>
                        <table>
                            <tr>
                                <td class="lbl">Pemohon</td>
                                <td class="val">{{ $rp->userRequest?->DES ?? $rp->RQ ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Pengirim</td>
                                <td class="val">{{ $rp->userConfirm1?->DES ?? $rp->CF1 ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Penerima</td>
                                <td class="val">{{ $rp->userConfirm2?->DES ?? $rp->CF2 ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>

            {{-- SECTION: DAFTAR BARANG YANG DIPINDAH --}}
            <h2 class="table-title">Daftar Barang Yang Dipindahkan</h2>
            <div class="table-container">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 35px;">No</th>
                            <th style="width: 150px;">Kode Barcode (BC_OUT)</th>
                            <th>Nama Barang</th>
                            <th style="width: 140px;">Kategori</th>
                            <th class="text-center" style="width: 100px;">Status Barang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $idx => $item)
                            @php
                                $itemStClass = match($item->SS) {
                                    'Ready'    => 'item-st-ready',
                                    'Dikirim'  => 'item-st-dikirim',
                                    'Diterima' => 'item-st-diterima',
                                    'Rusak'    => 'item-st-rusak',
                                    'Hilang'   => 'item-st-hilang',
                                    'Ditolak'  => 'item-st-ditolak',
                                    default    => 'item-st-ready',
                                };
                            @endphp
                            <tr>
                                <td class="text-center font-mono" style="color: #64748B;">{{ $idx + 1 }}</td>
                                <td class="font-mono font-semibold" style="color: #0F172A;">{{ $item->BC_OUT }}</td>
                                <td>{{ $item->NAMA_BARANG ?? '-' }}</td>
                                <td style="color: #475569;">{{ $item->NAMA_KATEGORI ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="item-st {{ $itemStClass }}">{{ $item->SS }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="color: #94A3B8; padding: 20px;">
                                    Tidak ada data barang perpindahan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- RINGKASAN TOTAL BARANG --}}
            <div class="summary-bar">
                <div class="summary-left">
                    Total Barang: {{ $countTotal }} Unit
                </div>
                <div class="summary-right">
                    @if($rp->SS === 'Diterima')
                        Diterima: <strong>{{ $countDiterima }}</strong> | Rusak: <strong>{{ $countRusak }}</strong> | Hilang: <strong>{{ $countHilang }}</strong>
                    @elseif($rp->SS === 'Pengiriman')
                        Dalam Pengiriman: <strong>{{ $countDikirim }}</strong> Unit
                    @elseif($rp->SS === 'Request')
                        Siap Dikirim: <strong>{{ $countReady }}</strong> Unit
                    @else
                        Status: <strong>{{ $rp->SS }}</strong>
                    @endif
                </div>
            </div>

            {{-- TANDA TANGAN (3 PIHAK) --}}
            <div class="signature-container">
                <div class="signature-box">
                    <div class="role">Pemohon (Request)</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $rp->userRequest?->DES ?? $rp->RQ ?? 'Petugas Request' }}</div>
                </div>

                <div class="signature-box">
                    <div class="role">Pengirim (Gudang Asal)</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $rp->userConfirm1?->DES ?? $rp->CF1 ?? 'Petugas Gudang Asal' }}</div>
                </div>

                <div class="signature-box">
                    <div class="role">Penerima (Gudang Tujuan)</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $rp->userConfirm2?->DES ?? $rp->CF2 ?? 'Petugas Gudang Tujuan' }}</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
