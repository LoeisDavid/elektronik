<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Pengiriman Barang ({{ $rp->RP }})</title>
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
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .main-title {
            font-size: 19px;
            font-weight: bold;
            color: #0D1C43;
            margin: 0 0 2px 0;
            letter-spacing: 0.5px;
        }

        .sub-title {
            font-size: 9.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .doc-number {
            font-size: 10px;
            color: #0D1C43;
            margin: 2px 0 0 0;
            font-weight: bold;
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
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        /* Info Box */
        .info-box-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 12px;
        }

        .info-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
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
            width: 85px;
        }

        .info-mini-table .val {
            color: #0f172a;
            font-weight: 600;
        }

        .delivery-instruction {
            background-color: #f1f5f9;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 8.5px;
            color: #334155;
            margin-bottom: 14px;
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
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .summary-bar-table td {
            border: none;
            padding: 0;
            color: #ffffff;
            font-size: 9px;
            vertical-align: middle;
        }

        /* Signatures (Pengirim CF1 & Penerima Manual) */
        .signature-table {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
            border-collapse: separate;
            border-spacing: 16px 0;
        }

        .sig-box {
            width: 50%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            text-align: center;
            vertical-align: top;
            background-color: #ffffff;
        }

        .sig-role {
            font-size: 9.5px;
            font-weight: bold;
            color: #0D1C43;
            margin-bottom: 2px;
        }

        .sig-subrole {
            font-size: 8px;
            color: #64748b;
        }

        .signature-space {
            height: 60px;
        }

        .signature-line-solid {
            border-bottom: 1.5px solid #0f172a;
            margin-bottom: 6px;
        }

        .signature-line-dotted {
            border-bottom: 1.5px dashed #64748b;
            margin-bottom: 6px;
        }

        .signature-name {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
        }

        .signature-manual-hint {
            font-size: 7.5px;
            color: #b45309;
            font-style: italic;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- Header Laporan -->
    <table class="header-table">
        <tr>
            <td>
                <div class="sub-title" style="margin-bottom: 2px; font-weight: bold; color: #0D1C43;">{{ config('perusahaan.nama_perusahaan', 'Pomelo.') }}</div>
                <div class="main-title">SURAT JALAN PENGIRIMAN BARANG</div>
                <div class="sub-title">Surat Pengantar Pemindahan Stok Antar Lokasi</div>
                <div class="doc-number">No. Surat Jalan: {{ $rp->RP }}</div>
                @if(config('perusahaan.alamat') || config('perusahaan.no_telp'))
                    <div style="font-size: 8px; color: #64748b; margin-top: 2px;">
                        {{ trim(collect([config('perusahaan.alamat'), config('perusahaan.kota')])->filter()->implode(', ')) }}
                        @if(config('perusahaan.no_telp'))
                            &bull; Telp: {{ config('perusahaan.no_telp') }}
                        @endif
                    </div>
                @endif
            </td>
            <td>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Status Dokumen</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">
                            <span class="status-badge">{{ $rp->SS }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tgl Kirim</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $rp->DTCF1 ? \Carbon\Carbon::parse($rp->DTCF1)->locale('id')->translatedFormat('d F Y') : $tanggalCetak }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Petugas Kirim</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $rp->userConfirm1?->DES ?? $rp->CF1 ?? $petugas }}</td>
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

    <!-- Info Box: Pengirim & Tujuan -->
    <table class="info-box-table">
        <tr>
            <!-- Kolom 1: Pengirim (Asal) -->
            <td class="info-card" style="width: 50%;">
                <div class="info-card-title">PENGIRIM (GUDANG ASAL)</div>
                <table class="info-mini-table">
                    <tr>
                        <td class="lbl">Lokasi Gudang</td>
                        <td class="val">: {{ $rp->gudangAsal?->DES ?? $rp->GD_ASAL }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Dikonfirmasi Oleh</td>
                        <td class="val">: <strong>{{ $rp->userConfirm1?->DES ?? $rp->CF1 ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="lbl">Tanggal Kirim</td>
                        <td class="val">: {{ $rp->DTCF1 ? \Carbon\Carbon::parse($rp->DTCF1)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                </table>
            </td>

            <!-- Kolom 2: Tujuan (Penerima) -->
            <td class="info-card" style="width: 50%;">
                <div class="info-card-title">TUJUAN (GUDANG TUJUAN)</div>
                <table class="info-mini-table">
                    <tr>
                        <td class="lbl">Lokasi Tujuan</td>
                        <td class="val">: <strong>{{ $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN }}</strong></td>
                    </tr>
                    <tr>
                        <td class="lbl">Penerima Barang</td>
                        <td class="val">: <em>(Diisi manual saat barang tiba)</em></td>
                    </tr>
                    <tr>
                        <td class="lbl">No. Polisi / Driver</td>
                        <td class="val">: ....................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="delivery-instruction">
        <strong>PENTING:</strong> Harap diperiksa kondisi dan jumlah fisik barang yang tercantum pada surat jalan ini dengan teliti. Petugas penerima di gudang tujuan wajib menandatangani kolom penerima secara manual sebagai bukti fisik penyerahan barang.
    </div>

    <!-- Section Title -->
    <div class="section-title">Rincian Barang Yang Dikirim ({{ $countTotal }} Unit)</div>

    <!-- Tabel Rincian Barang -->
    <table class="detail-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 28%;">Kode Barcode (BC_OUT)</th>
                <th style="width: 37%;">Nama Barang</th>
                <th style="width: 15%;">Merk / Kategori</th>
                <th class="text-center" style="width: 15%;">Checklist Fisik</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $idx => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td class="font-mono" style="font-weight: 600;">{{ $item->BC_OUT }}</td>
                    <td>{{ $item->NAMA_BARANG ?? '-' }}</td>
                    <td style="color: #475569;">
                        {{ $item->NAMA_MERK ?? '-' }}<br>
                        <span style="font-size: 7.5px; color: #64748b;">{{ $item->NAMA_KATEGORI ?? '-' }}</span>
                    </td>
                    <td class="text-center" style="color: #94a3b8;">
                        [ &nbsp; &nbsp; ] Baik / Lengkap
                    </td>
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
                Total Barang Dikirim: {{ $countTotal }} Unit
            </td>
            <td style="text-align: right;">
                Dokumen: <strong>Pengiriman Barang</strong>
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan: Pengirim & Penerima Manual -->
    <table class="signature-table">
        <tr>
            <!-- Kolom 1: Pengirim -->
            <td class="sig-box">
                <div class="sig-role">PENGIRIM (GUDANG ASAL)</div>
                <div class="sig-subrole">{{ $rp->gudangAsal?->DES ?? $rp->GD_ASAL }}</div>
                <div class="signature-space"></div>
                <div class="signature-line-solid"></div>
                <div class="signature-name">{{ $rp->userConfirm1?->DES ?? $rp->CF1 ?? 'Petugas Gudang Asal' }}</div>
            </td>

            <!-- Kolom 2: Penerima (Manual) -->
            <td class="sig-box">
                <div class="sig-role">PENERIMA (GUDANG TUJUAN)</div>
                <div class="sig-subrole">{{ $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN }} &bull; Diisi Manual</div>
                <div class="signature-space"></div>
                <div class="signature-line-dotted"></div>
                <div class="signature-name">( .................................................... )</div>
                <div class="signature-manual-hint">Nama Terang & Tanda Tangan Penerima</div>
            </td>
        </tr>
    </table>

</body>
</html>
