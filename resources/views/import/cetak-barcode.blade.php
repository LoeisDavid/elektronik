<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Hasil Import</title>
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
            font-weight: 600;
        }
        .bar a:hover, .bar button:hover { background: #d1d5db; border-color: #6b7280; }
        .bar button.btn-print,
        .bar button.btn-pdf {
            background-color: #0D1C43;
            color: #fff;
            border-color: #0D1C43;
        }
        .bar button.btn-print:hover,
        .bar button.btn-pdf:hover:not(:disabled) {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
        .bar button.btn-pdf:disabled {
            opacity: 0.7;
            cursor: wait;
        }

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
        <a href="{{ route('import.xls') }}">← Kembali ke Form Import</a>
        <button type="button" class="btn-print" onclick="window.print()">Print Barcode</button>
        <button type="button" id="btnExportPdf" class="btn-pdf">Export PDF</button>
        <span style="font-size: 13px; color: #555; align-self: center; margin-left: 10px;">
            Total: <strong>{{ count($cetak) }}</strong> label barcode
            @if (($mode ?? 'bc_out') === 'itm')
                <span style="color: #dc2626; font-weight: 600;">(Kode ITM)</span>
            @endif
        </span>
    </div>

    <div class="print-area">
        @foreach (array_chunk($cetak, 2) as $pair)
            <div class="label-row">
                @foreach ($pair as $r)
                    <div class="label">
                        <div class="nama" title="{{ $r['des'] }}">{{ $r['des'] }}</div>
                        <svg
                            class="barcode"
                            jsbarcode-value="{{ $r['barcode_value'] ?? $r['bc_out'] }}"
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
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            JsBarcode(".barcode").init();

            const btnExportPdf = document.getElementById('btnExportPdf');
            if (btnExportPdf) {
                btnExportPdf.addEventListener('click', exportBarcodePdf);
            }
        });

        function parseTranslate(transformAttr) {
            if (!transformAttr) return { tx: 0, ty: 0 };
            const match = transformAttr.match(/translate\(\s*([-\d.]+)(?:[\s,]+([-\d.]+))?\s*\)/);
            if (!match) return { tx: 0, ty: 0 };
            return {
                tx: parseFloat(match[1]) || 0,
                ty: parseFloat(match[2] || '0') || 0
            };
        }

        function extractBarcodeBars(svg) {
            if (!svg) return null;
            const bars = [];
            let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;

            svg.querySelectorAll('g rect').forEach(function(rect) {
                const fill = (rect.getAttribute('fill') || rect.style.fill || '').toLowerCase();
                if (fill === '#ffffff' || fill === 'white' || fill === 'rgb(255, 255, 255)') {
                    return;
                }
                const parentG = rect.closest('g');
                const offset = parseTranslate(parentG ? parentG.getAttribute('transform') : '');
                const x = (parseFloat(rect.getAttribute('x')) || 0) + offset.tx;
                const y = (parseFloat(rect.getAttribute('y')) || 0) + offset.ty;
                const w = parseFloat(rect.getAttribute('width')) || 0;
                const h = parseFloat(rect.getAttribute('height')) || 0;
                if (w <= 0 || h <= 0) return;

                bars.push({ x: x, y: y, w: w, h: h });
                if (x < minX) minX = x;
                if (x + w > maxX) maxX = x + w;
                if (y < minY) minY = y;
                if (y + h > maxY) maxY = y + h;
            });

            if (bars.length === 0) return null;

            return {
                bars: bars,
                minX: minX,
                minY: minY,
                width: maxX - minX,
                height: maxY - minY
            };
        }

        function fitTitleLines(doc, text, maxWidth) {
            let fontSize = 8.5;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(fontSize);
            let lines = doc.splitTextToSize(text, maxWidth);

            if (lines.length > 2) {
                fontSize = 7.5;
                doc.setFontSize(fontSize);
                lines = doc.splitTextToSize(text, maxWidth);
            }

            if (lines.length > 2) {
                let second = lines[1];
                while (second.length > 1 && doc.getTextWidth(second + '...') > maxWidth) {
                    second = second.slice(0, -1).trimEnd();
                }
                lines = [lines[0], second + '...'];
            }

            return { lines: lines, fontSize: fontSize };
        }

        function drawLabelCard(doc, labelEl, x, y, cardW, cardH) {
            const nama = (labelEl.querySelector('.nama')?.textContent || '').trim();
            const gudang = (labelEl.querySelector('.gudang')?.textContent || '').trim();
            const svg = labelEl.querySelector('svg.barcode');
            const bcOut = (svg?.getAttribute('jsbarcode-value') || '').trim();
            const centerX = x + cardW / 2;

            // Dashed border matching preview
            doc.setDrawColor(102, 102, 102);
            doc.setLineWidth(0.3);
            if (typeof doc.setLineDashPattern === 'function') {
                doc.setLineDashPattern([1.5, 1.5], 0);
            }
            doc.rect(x, y, cardW, cardH);
            if (typeof doc.setLineDashPattern === 'function') {
                doc.setLineDashPattern([], 0);
            }

            // Item name (top, max 2 lines)
            const title = fitTitleLines(doc, nama, cardW - 6);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(title.fontSize);
            doc.setTextColor(0, 0, 0);
            const titleStartY = title.lines.length > 1 ? y + 4.6 : y + 5.6;
            const lineStep = title.fontSize * 0.4;
            title.lines.forEach(function(line, idx) {
                doc.text(line, centerX, titleStartY + idx * lineStep, { align: 'center' });
            });

            // Vector barcode bars (middle)
            const barData = extractBarcodeBars(svg);
            const maxBarW = cardW - 8; // 84mm inside 92mm card
            const barBoxY = y + 11.4;
            const barBoxH = 17.4;

            if (barData && barData.width > 0 && barData.height > 0) {
                const scaleX = Math.min(maxBarW / barData.width, 0.36);
                const scaleY = barBoxH / barData.height;
                const drawW = barData.width * scaleX;
                const barStartX = x + (cardW - drawW) / 2;

                doc.setFillColor(0, 0, 0);
                barData.bars.forEach(function(b) {
                    doc.rect(
                        barStartX + (b.x - barData.minX) * scaleX,
                        barBoxY + (b.y - barData.minY) * scaleY,
                        b.w * scaleX,
                        b.h * scaleY,
                        'F'
                    );
                });
            }

            // Barcode code text
            if (bcOut) {
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8.5);
                doc.setTextColor(0, 0, 0);
                doc.text(bcOut, centerX, y + 32.8, { align: 'center' });
            }

            // Warehouse name (bottom)
            if (gudang) {
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7.5);
                doc.setTextColor(85, 85, 85);
                doc.text(gudang, centerX, y + 37.8, { align: 'center' });
            }
        }

        async function exportBarcodePdf() {
            const btn = document.getElementById('btnExportPdf');
            const labelNodes = Array.from(document.querySelectorAll('.print-area .label:not(.empty)'));
            if (labelNodes.length === 0) return;

            if (!window.jspdf || !window.jspdf.jsPDF) {
                alert('Library PDF gagal dimuat. Pastikan koneksi internet tersedia lalu muat ulang halaman.');
                return;
            }

            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Membuat PDF... 0%';

            // Open target tab synchronously on click so browser popup blocker allows it
            const pdfWindow = window.open('', '_blank');
            if (pdfWindow) {
                pdfWindow.document.title = 'Membuat PDF Barcode...';
                pdfWindow.document.body.innerHTML =
                    '<p style="font-family:sans-serif;padding:24px;color:#333;">Sedang membuat PDF (' +
                    labelNodes.length +
                    ' label barcode)... Mohon tunggu sebentar.</p>';
            }

            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4',
                    compress: true
                });

                // A4 Portrait (210x297mm): 10mm margins, 2 cols x 6 rows = 12 labels/page (92x42mm each)
                const marginX = 10;
                const marginY = 10;
                const colGap = 6;
                const rowGap = 5;
                const cardW = 92;
                const cardH = 42;
                const cols = 2;
                const rowsPerPage = 6;
                const perPage = cols * rowsPerPage;
                const chunkSize = 48;

                for (let i = 0; i < labelNodes.length; i++) {
                    if (i > 0 && i % perPage === 0) {
                        doc.addPage('a4', 'portrait');
                    }

                    const indexInPage = i % perPage;
                    const col = indexInPage % cols;
                    const row = Math.floor(indexInPage / cols);
                    const x = marginX + col * (cardW + colGap);
                    const y = marginY + row * (cardH + rowGap);

                    drawLabelCard(doc, labelNodes[i], x, y, cardW, cardH);

                    if ((i + 1) % chunkSize === 0 || i === labelNodes.length - 1) {
                        const pct = Math.round(((i + 1) / labelNodes.length) * 100);
                        btn.textContent = 'Membuat PDF... ' + pct + '%';
                        await new Promise(function(resolve) { setTimeout(resolve, 0); });
                    }
                }

                const blob = doc.output('blob');
                const blobUrl = URL.createObjectURL(blob);

                if (pdfWindow && !pdfWindow.closed) {
                    pdfWindow.location.href = blobUrl;
                } else {
                    const ts = new Date().toISOString().slice(0, 19).replace(/[-:T]/g, '');
                    doc.save('barcode-import-' + ts + '.pdf');
                }
            } catch (err) {
                if (pdfWindow && !pdfWindow.closed) {
                    pdfWindow.close();
                }
                console.error(err);
                alert('Gagal membuat PDF: ' + (err && err.message ? err.message : 'Terjadi kesalahan'));
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }
    </script>
</body>
</html>

