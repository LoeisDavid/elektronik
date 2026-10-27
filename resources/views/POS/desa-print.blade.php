<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Per Desa</title>
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

        /* Two Cards Grid via Table */
        .cards-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 16px;
        }

        .cards-table td.card-cell {
            width: 50%;
            vertical-align: top;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            border-radius: 6px;
            padding: 8px 10px;
        }

        .card-header-table {
            width: 100%;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 9.5px;
            color: #0f172a;
        }

        .card-unit {
            text-align: right;
            color: #64748b;
            font-size: 8.5px;
            font-weight: normal;
        }

        .bar-table {
            width: 100%;
            margin-bottom: 4px;
            font-size: 8.5px;
        }

        .bar-table td {
            padding: 2px 0;
            vertical-align: middle;
        }

        .bar-track {
            background-color: #f1f5f9;
            height: 6px;
            border-radius: 3px;
            width: 100%;
            overflow: hidden;
        }

        .bar-fill-blue {
            background-color: #2563eb;
            height: 6px;
            border-radius: 3px;
        }

        .bar-fill-green {
            background-color: #16a34a;
            height: 6px;
            border-radius: 3px;
        }

        /* Detail Table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 20px;
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

        .badge-nominal {
            display: inline-block;
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: bold;
            margin-left: 6px;
        }

        .badge-frekuensi {
            display: inline-block;
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: bold;
            margin-left: 6px;
        }

        /* Signature */
        .signature-table {
            width: 100%;
            margin-top: 28px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 40px;
        }

        .signature-line {
            border-bottom: 1px solid #94a3b8;
            height: 50px;
            margin-bottom: 6px;
        }

        .signature-name {
            font-size: 9px;
            color: #334155;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <!-- Header Laporan -->
    <table class="header-table">
        <tr>
            <td>
                <div class="sub-title">Laporan</div>
                <div class="main-title">Penjualan Per Desa</div>
            </td>
            <td>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Tanggal</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-val">{{ $tanggal }}</td>
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

    <!-- Section: Desa Pembelian Terdikit (2 Cards) -->
    <div class="section-title">Desa Pembelian Terdikit</div>
    <table class="cards-table">
        <tr>
            <!-- Top 5 Nominal Terdikit -->
            <td class="card-cell">
                <table class="card-header-table">
                    <tr>
                        <td>Top 5 Total Pembelian Terendah</td>
                        <td class="card-unit">Rp</td>
                    </tr>
                </table>
                @forelse ($top5Nominal as $item)
                    @php
                        $pct = $maxNominal > 0 ? round(($item->total_nominal / $maxNominal) * 100) : 0;
                        $jt = $item->total_nominal >= 1000000 
                            ? number_format($item->total_nominal / 1000000, 1, ',', '.') . ' jt'
                            : number_format($item->total_nominal / 1000, 0, ',', '.') . ' rb';
                    @endphp
                    <table class="bar-table">
                        <tr>
                            <td style="width: 32%; font-weight: 500;">{{ $item->DES }}</td>
                            <td style="width: 48%;">
                                <div class="bar-track">
                                    <div class="bar-fill-blue" style="width: {{ $pct }}%;"></div>
                                </div>
                            </td>
                            <td style="width: 20%; text-align: right; font-weight: bold; color: #1e293b;">{{ $jt }}</td>
                        </tr>
                    </table>
                @empty
                    <div style="font-size: 8.5px; color: #94a3b8; text-align: center; padding: 10px 0;">Belum ada data</div>
                @endforelse
            </td>

            <!-- Top 5 Frekuensi Terdikit -->
            <td class="card-cell">
                <table class="card-header-table">
                    <tr>
                        <td>Top 5 Total Transaksi Terendah</td>
                        <td class="card-unit">Kali</td>
                    </tr>
                </table>
                @forelse ($top5Frekuensi as $item)
                    @php
                        $pct = $maxFrekuensi > 0 ? round(($item->total_transaksi / $maxFrekuensi) * 100) : 0;
                    @endphp
                    <table class="bar-table">
                        <tr>
                            <td style="width: 32%; font-weight: 500;">{{ $item->DES }}</td>
                            <td style="width: 48%;">
                                <div class="bar-track">
                                    <div class="bar-fill-green" style="width: {{ $pct }}%;"></div>
                                </div>
                            </td>
                            <td style="width: 20%; text-align: right; font-weight: bold; color: #1e293b;">{{ $item->total_transaksi }}x</td>
                        </tr>
                    </table>
                @empty
                    <div style="font-size: 8.5px; color: #94a3b8; text-align: center; padding: 10px 0;">Belum ada data</div>
                @endforelse
            </td>
        </tr>
    </table>

    <!-- Section: Rincian Desa -->
    <div class="section-title">Rincian Penjualan Seluruh Desa</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 7%;">No</th>
                <th style="width: 48%;">Nama Desa</th>
                <th class="text-center" style="width: 18%;">Transaksi</th>
                <th class="text-right" style="width: 27%;">Total Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($desaList as $index => $desa)
                @php
                    $isTerdikitNominal = ($desa->DS === $desaNominalTerdikit);
                    $isTerdikitFrekuensi = ($desa->DS === $desaFrekuensiTerdikit);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span>{{ $desa->DES }}</span>
                        @if ($isTerdikitNominal)
                            <span class="badge-nominal" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171;">Nominal terendah</span>
                        @endif
                        @if ($isTerdikitFrekuensi && !$isTerdikitNominal)
                            <span class="badge-frekuensi" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">Transaksi terendah</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $desa->total_transaksi }}</td>
                    <td class="text-right" style="font-weight: 600;">Rp {{ number_format($desa->total_nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 16px; color: #94a3b8;">
                        Belum ada data penjualan per desa
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                <div class="signature-name">Dibuat oleh ({{ $petugas }})</div>
            </td>
            <td>
                <div class="signature-line"></div>
                <div class="signature-name">Diperiksa oleh</div>
            </td>
        </tr>
    </table>

</body>
</html>
