<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 20px; }
        .bar { display: flex; gap: 8px; margin-bottom: 16px; }
        .bar a, .bar button {
            padding: 8px 14px;
            border: 1px solid #999;
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            text-decoration: none;
            color: #000;
            font-size: 13px;
        }
        .bar a:hover, .bar button:hover { background: #d1d5db; border-color: #6b7280; }

        .print-area {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .label-row {
            display: grid;
            grid-template-columns: repeat(2, 260px);
            column-gap: 16px;
        }
        .label {
            border: 1px dashed #666;
            padding: 10px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            break-inside: avoid;
        }
        .label.empty {
            border-color: transparent;
        }
        .label svg.barcode {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .nama {
            width: 100%;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 4px;
            text-align: center;
        }
        .gudang {
            width: 100%;
            font-size: 10px;
            color: #555;
            margin-top: 4px;
            text-align: center;
        }

        /* Print layout for Zebra ZD220: 2-across 50x30mm labels + 3mm gap (103x30mm total) */
        @page {
            size: 103mm 30mm;
            margin: 0 !important;
        }
        @media print {
            html, body {
                width: 103mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff;
            }
            .bar {
                display: none !important;
            }
            .print-area {
                display: block;
                gap: 0;
                margin: 0;
                padding: 0;
            }
            .label-row {
                width: 103mm;
                height: 30mm;
                display: grid;
                grid-template-columns: 50mm 50mm;
                column-gap: 3mm;
                margin: 0;
                padding: 0;
                page-break-after: always;
                break-after: page;
                page-break-inside: avoid;
                break-inside: avoid;
                overflow: hidden;
            }
            .label-row:last-child {
                page-break-after: auto;
                break-after: auto;
            }
            .label {
                width: 50mm;
                height: 30mm;
                border: none !important;
                padding: 1.5mm 1.5mm;
                margin: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                overflow: hidden;
                /* Shift +1mm right to compensate for 1mm left / 3mm right liner offset */
                transform: translateX(1mm);
            }
            .nama {
                width: 47mm;
                font-size: 7.5pt;
                font-weight: 700;
                line-height: 1.15;
                margin: 0 0 0.5mm 0;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .label svg.barcode {
                width: 46mm !important;
                max-width: 46mm !important;
                height: 18mm !important;
                display: block;
                margin: 0 auto !important;
            }
            .gudang {
                width: 47mm;
                font-size: 6.5pt;
                color: #000;
                line-height: 1.1;
                margin: 0.5mm 0 0 0;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    </style>
</head>
<body>
    <div class="bar">
        <a href="{{ url()->previous() ?: route('inventory.item') }}">← Kembali</a>
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <div class="print-area">
        @foreach (array_chunk($cetak, 2) as $pair)
            <div class="label-row">
                @foreach ($pair as $r)
                    <div class="label">
                        <div class="nama">{{ $r['des'] }}</div>
                        <svg
                            class="barcode"
                            jsbarcode-value="{{ $r['bc_out'] }}"
                            jsbarcode-format="CODE128"
                            jsbarcode-width="2"
                            jsbarcode-height="90"
                            jsbarcode-fontsize="16"
                            jsbarcode-textmargin="3"
                            jsbarcode-margin="0"
                            jsbarcode-displayvalue="true"
                        ></svg>
                        <div class="gudang">{{ $r['gd'] }}</div>
                    </div>
                @endforeach
                @if (count($pair) < 2)
                    <div class="label empty"></div>
                @endif
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>JsBarcode(".barcode").init();</script>
</body>
</html>
