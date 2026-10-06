<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .bar { display: flex; gap: 8px; margin-bottom: 16px; }
        .bar a, .bar button { padding: 8px 14px; border: 1px solid #999; border-radius: 6px; background: #fff; cursor: pointer; text-decoration: none; color: #000; font-size: 13px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .label { border: 1px dashed #666; padding: 10px; text-align: center; break-inside: avoid; }
        .label svg { max-width: 100%; height: auto; }
        .nama { font-size: 12px; font-weight: bold; margin-bottom: 4px; }
        .gudang { font-size: 10px; color: #555; }
        @media print { .bar { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="bar">
        <a href="{{ url()->previous() ?: route('inventory.item') }}">← Kembali</a>
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <div class="grid">
        @foreach ($cetak as $r)
            <div class="label">
                <div class="nama">{{ $r['des'] }}</div>
                <svg
                    class="barcode"
                    jsbarcode-value="{{ $r['bc_out'] }}"
                    jsbarcode-format="CODE128"
                    jsbarcode-width="2"
                    jsbarcode-height="60"
                    jsbarcode-fontsize="14"
                    jsbarcode-displayvalue="true"
                ></svg>
                <div class="gudang">{{ $r['gd'] }}</div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>JsBarcode(".barcode").init();</script>
</body>
</html>