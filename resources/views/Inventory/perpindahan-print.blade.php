<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perpindahan Stok ({{ $rp->RP }})</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 14mm 14mm 14mm 14mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .main-title {
            font-size: 20px;
            font-weight: bold;
            color: #0D1C43;
            margin: 0 0 2px 0;
        }

        .sub-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .doc-number {
            font-size: 9.5px;
            color: #475569;
            margin: 2px 0 0 0;
        }

        .meta-table {
            float: right;
            border-collapse: collapse;
            font-size: 9.5px;
        }

        .meta-table td {
            padding: 1.5px 4px;
        }

        .meta-label {
            color: #64748b;
            text-align: right;
        }

        .meta-colon {
            width: 6px;
            text-align: center;
        }

        .meta-val {
            font-weight: bold;
            color: #0f172a;
        }

        .divider-line {
            border-top: 1.5px solid #0D1C43;
            margin: 10px 0 14px 0;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8px;
        }
        .badge-request    { background-color: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .badge-pengiriman { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-diterima   { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-ditolak    { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* Info Box via Table */
        .info-box-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 14px;
        }

        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            vertical-align: top;
        }

        .info-card-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #0D1C43;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }

        .info-mini-table {
            width: 100%;
            font-size: 8.5px;
        }

        .info-mini-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-mini-table .lbl {
            color: #64748b;
            width: 75px;
        }

        .info-mini-table .val {
            color: #0f172a;
            font-weight: 600;
        }

        /* Detail Table */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 12px;
        }

        .detail-table th {
            background-color: #0D1C43;
            color: #ffffff;
            padding: 6px 8px;
            font-weight: bold;
            border: 1px solid #0D1C43;
            font-size: 8.5px;
        }

        .detail-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .detail-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .font-mono   { font-family: 'DejaVu Sans', monospace; font-size: 8px; }

        .item-st {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .item-st-ready    { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .item-st-dikirim  { background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .item-st-diterima { background-color: #dcfce7; color: #16a34a; border: 1px solid #86efac; }
        .item-st-rusak    { background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .item-st-hilang   { background-color: #ffedd5; color: #ea580c; border: 1px solid #fed7aa; }

        /* Summary Bar */
        .summary-bar-table {
            width: 100%;
            background-color: #0D1C43;
            color: #ffffff;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 24px;
            page-break-inside: avoid;
        }

        .summary-bar-table td {
            border: none;
            padding: 0;
            color: #ffffff;
            font-size: 9px;
            vertical-align: middle;
        }

        /* Signatures (3 Pihak) */
        .signature-table {
            width: 100%;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 16px;
        }

        .sig-role {
            font-size: 8.5px;
            color: #64748b;
            font-weight: 500;
        }

        .signature-line {
            border-bottom: 1px solid #94a3b8;
            height: 48px;
            margin-bottom: 6px;
        }

        .signature-name {
            font-size: 8.5px;
            color: #1e293b;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header Laporan -->
    <table class="header-table">
        <tr>
            <td>
                <div class="sub-title">Laporan</div>
                <div class="main-title">Perpindahan Stok Barang</div>
                <div class="doc-number">No. Dokumen: <strong>{{ $rp->RP }}</strong></div>
            </td>
            <td>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Status</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">
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
                        <td class="meta-label">Tanggal Cetak</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $tanggalCetak }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Petugas</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $petugas }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Lokasi</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $lokasi }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="divider-line"></div>

    <!-- Box Informasi Perpindahan (3 Kolom) -->
    <table class="info-box-table">
        <tr>
            <!-- Kolom 1: Gudang -->
            <td class="info-card" style="width: 33.33%;">
                <div class="info-card-title">Rute Perpindahan</div>
                <table class="info-mini-table">
                    <tr>
                        <td class="lbl">Gudang Asal</td>
                        <td class="val">: {{ $rp->gudangAsal?->DES ?? $rp->GD_ASAL }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Gudang Tujuan</td>
                        <td class="val">: {{ $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN }}</td>
                    </tr>
                </table>
            </td>

            <!-- Kolom 2: Tanggal -->
            <td class="info-card" style="width: 33.33%;">
                <div class="info-card-title">Riwayat Tanggal</div>
                <table class="info-mini-table">
                    <tr>
                        <td class="lbl">Tgl Request</td>
                        <td class="val">: {{ $rp->DTRQ ? \Carbon\Carbon::parse($rp->DTRQ)->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tgl Pengiriman</td>
                        <td class="val">: {{ $rp->DTCF1 ? \Carbon\Carbon::parse($rp->DTCF1)->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tgl Diterima</td>
                        <td class="val">: {{ $rp->DTCF2 ? \Carbon\Carbon::parse($rp->DTCF2)->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                    </tr>
                </table>
            </td>

            <!-- Kolom 3: Penanggung Jawab -->
            <td class="info-card" style="width: 33.33%;">
                <div class="info-card-title">Penanggung Jawab</div>
                <table class="info-mini-table">
                    <tr>
                        <td class="lbl">Pemohon</td>
                        <td class="val">: {{ $rp->userRequest?->DES ?? $rp->RQ ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Pengirim</td>
                        <td class="val">: {{ $rp->userConfirm1?->DES ?? $rp->CF1 ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Penerima</td>
                        <td class="val">: {{ $rp->userConfirm2?->DES ?? $rp->CF2 ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Section Title -->
    <div class="section-title">Daftar Barang Yang Dipindahkan</div>

    <!-- Tabel Rincian Barang -->
    <table class="detail-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">No</th>
                <th style="width: 34%;">Kode Barcode (BC_OUT)</th>
                <th style="width: 40%;">Nama Barang</th>
                <th style="width: 20%;">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $idx => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td class="font-mono" style="font-weight: 600;">{{ $item->BC_OUT }}</td>
                    <td>{{ $item->NAMA_BARANG ?? '-' }}</td>
                    <td style="color: #475569;">{{ $item->NAMA_KATEGORI ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #94a3b8; padding: 16px;">
                        Tidak ada data barang perpindahan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Bar Total Barang -->
    <table class="summary-bar-table">
        <tr>
            <td style="text-align: left; font-weight: bold;">
                Total Barang: {{ $countTotal }} Unit
            </td>
            <td style="text-align: right;">
                Status Dokumen: <strong>{{ $rp->SS }}</strong>
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan (3 Pihak) -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-role">Pemohon (Request)</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $rp->userRequest?->DES ?? $rp->RQ ?? 'Petugas Request' }}</div>
            </td>
            <td>
                <div class="sig-role">Pengirim (Gudang Asal)</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $rp->userConfirm1?->DES ?? $rp->CF1 ?? 'Petugas Gudang Asal' }}</div>
            </td>
            <td>
                <div class="sig-role">Penerima (Gudang Tujuan)</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $rp->userConfirm2?->DES ?? $rp->CF2 ?? 'Petugas Gudang Tujuan' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
