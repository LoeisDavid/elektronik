<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
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
            margin-bottom: 20px;
        }

        .detail-table th {
            background-color: #0D1C43;
            color: #ffffff;
            padding: 6px 6px;
            font-weight: bold;
            border: 1px solid #0D1C43;
            font-size: 8.5px;
        }

        .detail-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .text-left   { text-align: left; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .font-mono   { font-family: 'DejaVu Sans', monospace; font-size: 8px; }

        /* Invoice sub-header row */
        .invoice-header-row td {
            background-color: #f1f5f9;
            border-top: 1.5px solid #94a3b8;
            border-bottom: 1px solid #cbd5e1;
            padding: 5px 8px;
        }

        .inv-head-table {
            width: 100%;
        }

        .inv-head-table td {
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
        }

        .inv-head-code {
            font-weight: bold;
            color: #0D1C43;
            margin-right: 8px;
        }

        .inv-head-meta {
            color: #475569;
            margin-right: 8px;
        }

        .inv-head-right {
            text-align: right;
            font-weight: bold;
            color: #0F172A;
        }

        .item-row:nth-child(even) td {
            background-color: #fafafa;
        }

        .summary-row td {
            border: none;
            padding: 2px 6px;
        }

        .sub-label {
            text-align: right;
            font-weight: 500;
            color: #475569;
        }

        .sub-val {
            text-align: right;
            font-weight: 600;
            color: #1e293b;
        }

        .invoice-total-row td {
            border-top: 1px solid #cbd5e1;
            border-bottom: 1.5px solid #94a3b8;
            padding: 4px 6px 6px;
            background-color: #f8fafc;
        }

        .tot-label {
            text-align: right;
            font-weight: bold;
            color: #0D1C43;
            font-size: 9px;
        }

        .tot-val {
            text-align: right;
            font-weight: bold;
            color: #0D1C43;
            font-size: 9.5px;
        }

        /* Grand Total Row (Memanjang seperti Margin Transaksi dengan warna biru Grand Total) */
        .grand-total-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 24px;
            page-break-inside: avoid;
            background-color: #0D1C43;
            color: #ffffff;
        }

        .grand-total-table td {
            background-color: #0D1C43;
            color: #ffffff;
            padding: 8px 10px;
            border: 1px solid #0D1C43;
            vertical-align: middle;
        }

        .grand-total-margin {
            text-align: left;
            font-size: 9px;
            color: #cbd5e1;
        }

        .grand-total-label {
            text-align: right;
            font-size: 9.5px;
            font-weight: bold;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .grand-total-val {
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            white-space: nowrap;
        }

        /* Signature */
        .signature-table {
            width: 100%;
            margin-top: 28px;
            page-break-inside: avoid;
            clear: both;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }

        .sig-box {
            width: 220px;
            margin: 0 auto;
            text-align: center;
        }

        .sig-date {
            font-size: 8.5px;
            color: #475569;
            margin-bottom: 4px;
            min-height: 12px;
        }

        .sig-title {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .sig-space {
            height: 55px;
        }

        .sig-line {
            width: 180px;
            margin: 0 auto 5px auto;
            border-bottom: 1px solid #64748b;
        }

        .sig-name {
            font-size: 9px;
            color: #0f172a;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header Laporan -->
    <table class="header-table">
        <tr>
            <td>
                <div class="sub-title">{{ config('perusahaan.nama_perusahaan', 'Pomelo.') }} &bull; Laporan</div>
                <div class="main-title">Penjualan</div>
                @if(config('perusahaan.alamat') || config('perusahaan.no_telp'))
                    <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">
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
                        <td class="meta-label">Tanggal</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $tanggalDisplay }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Petugas</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $petugas }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Lokasi</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $lokasi ?? config('perusahaan.kota', 'Sumenep') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="divider-line"></div>

    <!-- Section Title -->
    <div class="section-title">Rincian Penjualan</div>

    <!-- Tabel Rincian Penjualan -->
    <table class="detail-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 15%;">Kode Barang</th>
                <th class="text-left" style="width: 29%;">Nama Barang</th>
                <th class="text-right" style="width: 12%;">Harga Satuan</th>
                <th class="text-center" style="width: 9%;">Diskon</th>
                <th class="text-right" style="width: 11%;">Voucher</th>
                <th class="text-right" style="width: 12%;">Sub Total</th>
                <th class="text-right" style="width: 12%;">Margin (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reportData as $inv)
                <!-- Header Per Transaksi / Invoice -->
                <tr class="invoice-header-row">
                    <td colspan="7">
                        <table class="inv-head-table">
                            <tr>
                                <td style="text-align: left;">
                                    <span class="inv-head-code">{{ $inv['ref_no'] }}</span>
                                    <span class="inv-head-meta">{{ $inv['waktu'] }}</span>
                                    <span class="inv-head-meta">&bull; Pelanggan: <strong>{{ $inv['customer'] }}</strong></span>
                                    @if(!empty($inv['desa']) && $inv['desa'] !== '-')
                                        <span class="inv-head-meta">({{ $inv['desa'] }})</span>
                                    @endif
                                </td>
                                <td class="inv-head-right">
                                    {{ $inv['kasir_metode'] }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Item-item Invoice -->
                @foreach ($inv['items'] as $item)
                    <tr class="item-row">
                        <td class="font-mono">{{ $item['kode'] }}</td>
                        <td>{{ $item['nama'] }}</td>
                        <td class="text-right">{{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item['diskon_persen'] > 0 ? $item['diskon_persen'] . '%' : '-' }}</td>
                        <td class="text-right">{{ $item['voucher_satuan'] > 0 ? '-' . number_format($item['voucher_satuan'], 0, ',', '.') : '-' }}</td>
                        <td class="text-right" style="font-weight: 600;">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        <td class="text-right" style="font-weight: 600; color: {{ $item['margin_pct'] >= 0 ? '#0D1C43' : '#dc2626' }};">
                            {{ number_format($item['margin_pct'], 1, ',', '.') }}%
                        </td>
                    </tr>
                @endforeach

                <!-- Breakdown Invoice -->
                <tr class="summary-row">
                    <td colspan="5"></td>
                    <td class="sub-label">Sub Total</td>
                    <td class="sub-val">{{ number_format($inv['subtotal'], 0, ',', '.') }}</td>
                </tr>
                @if($inv['diskon_global'] > 0)
                <tr class="summary-row">
                    <td colspan="5"></td>
                    <td class="sub-label">Diskon Global</td>
                    <td class="sub-val">-{{ number_format($inv['diskon_global'], 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($inv['voucher_global'] > 0)
                <tr class="summary-row">
                    <td colspan="5"></td>
                    <td class="sub-label">Voucher Global</td>
                    <td class="sub-val">-{{ number_format($inv['voucher_global'], 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($inv['ppn_nominal'] > 0)
                <tr class="summary-row">
                    <td colspan="5"></td>
                    <td class="sub-label">PPN ({{ $inv['ppn_pct'] }}%)</td>
                    <td class="sub-val">{{ number_format($inv['ppn_nominal'], 0, ',', '.') }}</td>
                </tr>
                @endif

                <!-- Total Row -->
                <tr class="invoice-total-row">
                    <td colspan="3" style="text-align: left; font-size: 8.5px; color: #475569; padding-left: 8px;">
                        Margin Transaksi: <strong style="color: {{ $inv['margin_pct'] >= 0 ? '#0D1C43' : '#dc2626' }}; font-size: 9px;">{{ number_format($inv['margin_pct'], 1, ',', '.') }}%</strong>
                    </td>
                    <td colspan="3" class="tot-label">Total {{ $inv['ref_no'] }}</td>
                    <td class="tot-val">Rp {{ number_format($inv['total'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data transaksi pada rentang tanggal yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Grand Total Keseluruhan -->
    <table class="grand-total-table">
        <tr>
            <td class="grand-total-margin">
                Margin Keseluruhan: <strong style="color: #ffffff; font-size: 10px;">{{ number_format($grandTotalMarginPct, 1, ',', '.') }}%</strong>
            </td>
            <td class="grand-total-label">
                Grand Total Keseluruhan
            </td>
            <td class="grand-total-val" style="width: 18%;">
                Rp {{ number_format($grandTotalKeseluruhan, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-box">
                    <div class="sig-date">&nbsp;</div>
                    <div class="sig-title">Dibuat oleh,</div>
                    <div class="sig-space"></div>
                    <div class="sig-line"></div>
                    <div class="sig-name">( {{ $petugas }} )</div>
                </div>
            </td>
            <td>
                <div class="sig-box">
                    <div class="sig-date">{{ $lokasi }}, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</div>
                    <div class="sig-title">Diperiksa oleh,</div>
                    <div class="sig-space"></div>
                    <div class="sig-line"></div>
                    <div class="sig-name">( .................................................... )</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
