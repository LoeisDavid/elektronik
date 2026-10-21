<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Cek Stok</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
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
            font-size: 10px;
        }

        .meta-table td {
            padding: 2px 4px;
        }

        .meta-label {
            color: #64748b;
            text-align: right;
        }

        .meta-val {
            font-weight: bold;
            color: #0f172a;
            padding-left: 8px;
        }

        .divider {
            border-top: 1.5px solid #0D1C43;
            margin: 12px 0 16px 0;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Ringkasan Cards via Table */
        .summary-table {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }

        .summary-card {
            padding: 8px 10px;
            border-radius: 6px;
            text-align: center;
        }

        .card-label {
            font-size: 9px;
            font-weight: 500;
            margin-bottom: 4px;
            text-align: left;
        }

        .card-num {
            font-size: 22px;
            font-weight: bold;
            line-height: 1.1;
        }

        .card-total {
            background-color: #0D1C43;
            color: #ffffff;
            border: 1px solid #0D1C43;
        }
        .card-total .card-label { color: #cbd5e1; }
        .card-total .card-num { color: #ffffff; }

        .card-terjual {
            background-color: #E8F8EA;
            border: 1px solid #86EFAC;
        }
        .card-terjual .card-label { color: #166534; }
        .card-terjual .card-num { color: #16A34A; }

        .card-belum {
            background-color: #FFFBEB;
            border: 1px solid #FDE047;
        }
        .card-belum .card-label { color: #CA8A04; }
        .card-belum .card-num { color: #EAB308; }

        .card-checking {
            background-color: #F3F4F6;
            border: 1px solid #D1D5DB;
        }
        .card-checking .card-label { color: #4B5563; }
        .card-checking .card-num { color: #6B7280; }

        /* Detail Table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 24px;
        }

        .detail-table th {
            background-color: #0D1C43;
            color: #ffffff;
            padding: 7px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #0D1C43;
        }

        .detail-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .detail-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .td-center { text-align: center; }
        .td-code { font-family: 'DejaVu Sans', monospace; font-size: 8.5px; }

        /* Status Pills */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }

        .badge-sudah_terjual {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .badge-belum_terjual {
            background-color: #fef9c3;
            color: #a16207;
            border: 1px solid #fde047;
        }

        .badge-checking {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 40px;
        }

        .sig-line {
            border-bottom: 1px solid #94a3b8;
            height: 55px;
            margin-bottom: 6px;
        }

        .sig-name {
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
                <div class="main-title">Cek Stok</div>
            </td>
            <td>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Tanggal :</td>
                        <td class="meta-val">{{ $tanggal }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Petugas :</td>
                        <td class="meta-val">{{ $petugas }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Ringkasan -->
    <div class="section-title">Ringkasan</div>
    <table class="summary-table">
        <tr>
            <td style="width: 25%;">
                <div class="summary-card card-total">
                    <div class="card-label">Total Data</div>
                    <div class="card-num">{{ $summary['total'] ?? 0 }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card card-terjual">
                    <div class="card-label">Sudah Terjual</div>
                    <div class="card-num">{{ $summary['sudah_terjual'] ?? 0 }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card card-belum">
                    <div class="card-label">Belum Terjual</div>
                    <div class="card-num">{{ $summary['belum_terjual'] ?? 0 }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card card-checking">
                    <div class="card-label">Tidak Terscan</div>
                    <div class="card-num">{{ $summary['tidak_terscan'] ?? 0 }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Rincian Barang -->
    <div class="section-title">Rincian Barang</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 32%;">Kode Barcode</th>
                <th style="width: 44%;">Nama Barang</th>
                <th style="width: 18%; text-align: center;">Status</th>
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
                    <td class="td-center">{{ $idx + 1 }}</td>
                    <td class="td-code">{{ $row['kode_barcode'] }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td class="td-center">
                        <span class="badge badge-{{ $status }}">{{ $statusLabel }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="td-center" style="padding: 16px; color: #94a3b8;">
                        Tidak ada data barang.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-line"></div>
                <div class="sig-name">Dibuat oleh ({{ $petugas }})</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-name">Diperiksa oleh</div>
            </td>
        </tr>
    </table>

</body>
</html>
