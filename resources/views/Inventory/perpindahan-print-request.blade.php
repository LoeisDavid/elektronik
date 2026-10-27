<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permintaan Perpindahan Barang ({{ $rp->RP }})</title>
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

        .stage-tag {
            display: inline-block;
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .main-title {
            font-size: 18px;
            font-weight: bold;
            color: #0D1C43;
            margin: 0 0 2px 0;
        }

        .sub-title {
            font-size: 9.5px;
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
            background-color: #fef9c3;
            color: #a16207;
            border: 1px solid #fde047;
        }

        /* Info Box */
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
            width: 80px;
        }

        .info-mini-table .val {
            color: #0f172a;
            font-weight: 600;
        }

        /* Detail Table */
        .section-title {
            font-size: 10.5px;
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

        /* 1 Tanda Tangan Saja (Pemohon) */
        .single-signature-table {
            width: 100%;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .sig-container {
            width: 220px;
            float: right;
            text-align: center;
        }

        .sig-date {
            font-size: 8.5px;
            color: #475569;
            margin-bottom: 4px;
        }

        .sig-role {
            font-size: 9px;
            color: #0D1C43;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .sig-subrole {
            font-size: 7.5px;
            color: #64748b;
        }

        .signature-line {
            border-bottom: 1.5px solid #0f172a;
            height: 60px;
            margin-bottom: 6px;
        }

        .signature-name {
            font-size: 9px;
            color: #0f172a;
            font-weight: bold;
        }

        .sig-note {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
        }

        .notice-box {
            background-color: #f8fafc;
            border-left: 3px solid #0D1C43;
            padding: 6px 10px;
            font-size: 8px;
            color: #475569;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <!-- Header Laporan -->
    <table class="header-table">
        <tr>
            <td>
                <div class="main-title">SURAT PERMINTAAN PERPINDAHAN BARANG</div>
                <div class="sub-title">Dokumen Pengajuan Pemindahan Stok Antar Gudang</div>
                <div class="doc-number">No. Dokumen: <strong>{{ $rp->RP }}</strong></div>
            </td>
            <td>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Status</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">
                            <span class="status-badge">{{ $rp->SS }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal Cetak</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $tanggalCetak }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Petugas Cetak</td>
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

    <!-- Box Informasi Permintaan (2 Kolom) -->
    <table class="info-box-table">
        <tr>
            <!-- Kolom 1: Rute Gudang -->
            <td class="info-card" style="width: 50%;">
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

            <!-- Kolom 2: Detail Pengajuan -->
            <td class="info-card" style="width: 50%;">
                <div class="info-card-title">Detail Pengajuan</div>
                <table class="info-mini-table">
                    <tr>
                        <td class="lbl">Tgl Request</td>
                        <td class="val">: {{ $rp->DTRQ ? \Carbon\Carbon::parse($rp->DTRQ)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Diajukan Oleh</td>
                        <td class="val">: {{ $rp->userRequest?->DES ?? $rp->RQ ?? '-' }} ({{ $rp->RQ }})</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="notice-box">
        Dokumen ini merupakan surat resmi permintaan pemindahan unit barang dari <strong>{{ $rp->gudangAsal?->DES ?? $rp->GD_ASAL }}</strong> menuju <strong>{{ $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN }}</strong> dan menunggu proses konfirmasi pengiriman oleh gudang asal.
    </div>

    <!-- Section Title -->
    <div class="section-title">Daftar Barang Yang Diminta ({{ $countTotal }} Unit)</div>

    <!-- Tabel Rincian Barang -->
    <table class="detail-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">No</th>
                <th style="width: 32%;">Kode Barcode (BC_OUT)</th>
                <th style="width: 38%;">Nama Barang</th>
                <th style="width: 14%;">Merk</th>
                <th style="width: 10%;">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $idx => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td class="font-mono" style="font-weight: 600;">{{ $item->BC_OUT }}</td>
                    <td>{{ $item->NAMA_BARANG ?? '-' }}</td>
                    <td style="color: #475569;">{{ $item->NAMA_MERK ?? '-' }}</td>
                    <td style="color: #475569;">{{ $item->NAMA_KATEGORI ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #94a3b8; padding: 16px;">
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
                Status: <strong>Permintaan (Request)</strong>
            </td>
        </tr>
    </table>

    <!-- 1 Tanda Tangan Saja (Pemohon / RQ) -->
    <table class="single-signature-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 8px; color: #64748b; line-height: 1.5;">
                    <em>Catatan:</em><br>
                    Surat permintaan ini diterbitkan otomatis oleh sistem saat pengajuan perpindahan stok.<br>
                    Barang fisik akan disiapkan dan dikirim setelah disetujui pada proses pengiriman.
                </div>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <div class="sig-container">
                    <div class="sig-date">{{ $lokasi }}, {{ $rp->DTRQ ? \Carbon\Carbon::parse($rp->DTRQ)->locale('id')->translatedFormat('d F Y') : $tanggalCetak }}</div>
                    <div class="sig-role">Pemohon (Yang Mengajukan)</div>
                    <div class="sig-subrole">Petugas Request</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $rp->userRequest?->DES ?? $rp->RQ ?? 'Petugas Request' }}</div>
                    <div class="sig-note">ID: {{ $rp->RQ }}</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
