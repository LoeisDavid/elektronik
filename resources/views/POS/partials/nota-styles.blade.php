<style>
    .nota-root * {
        box-sizing: border-box;
    }

    .nota-root {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        min-width: 215mm;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        color: #111111;
    }

    .nota-root .sheet {
        position: relative;
        width: 215mm;
        height: 110mm;
        padding: 5mm;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        text-align: left;
    }

    /* Screen-only guide showing physical pre-printed ink/watermark on paper (hidden on print) */
    .nota-root .preprinted-guide {
        position: absolute;
        inset: 0;
        pointer-events: none;
        user-select: none;
        z-index: 0;
    }

    .nota-root .pp-header {
        position: absolute;
        left: 14mm;
        top: 10mm;
        line-height: 1.25;
        color: #6b7280;
    }

    .nota-root .pp-shop {
        font-family: Georgia, "Times New Roman", Times, serif;
        font-size: 11pt;
        font-weight: 700;
        color: rgba(217, 119, 6, 0.78);
        letter-spacing: 0.4px;
    }

    .nota-root .pp-meta {
        font-size: 8.5pt;
        color: rgba(55, 65, 81, 0.72);
    }

    .nota-root .pp-watermark {
        position: absolute;
        left: 10mm;
        right: 42mm;
        top: 46mm;
        text-align: center;
        font-family: Georgia, "Times New Roman", Times, serif;
        font-size: 36pt;
        font-weight: 700;
        letter-spacing: 3px;
        color: rgba(148, 163, 184, 0.22);
        white-space: nowrap;
    }

    .nota-root .pp-tagline {
        position: absolute;
        left: 10mm;
        right: 42mm;
        top: 80mm;
        text-align: center;
        font-family: Georgia, "Times New Roman", Times, serif;
        font-size: 9pt;
        letter-spacing: 1px;
        color: rgba(148, 163, 184, 0.35);
    }

    .nota-root .pp-ppn {
        position: absolute;
        right: 39mm;
        bottom: 10.5mm;
        font-size: 7.5pt;
        font-weight: 700;
        color: rgba(31, 41, 55, 0.68);
        letter-spacing: 0.3px;
    }

    .nota-root .pp-right-strip {
        position: absolute;
        top: 4mm;
        bottom: 4mm;
        right: 3mm;
        width: 31mm;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nota-root .pp-right-text {
        writing-mode: vertical-rl;
        text-align: center;
        font-size: 6.8pt;
        line-height: 1.35;
        color: rgba(59, 130, 246, 0.52);
        letter-spacing: 0.2px;
    }

    .nota-root .pp-right-text strong {
        color: rgba(220, 38, 38, 0.58);
        font-weight: 700;
    }

    /* Printable area: clears pre-printed top-left header (top 25mm), left margin (9mm), right PERHATIAN strip (32mm) */
    .nota-root .sheet-inner {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100mm;
        padding: 25mm 32mm 0 9mm;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        font-size: 9pt;
        line-height: 1.2;
        color: #111111;
    }

    .nota-root .label {
        color: #555555;
        font-weight: 400;
    }

    .nota-root .value {
        color: #111111;
        font-weight: 500;
    }

    .nota-root .mono {
        font-family: "Courier New", Courier, monospace;
        letter-spacing: 0.4px;
    }

    .nota-root .num {
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    /* 1. No. Transaksi placed directly below pre-printed NPWP */
    .nota-root .trx-row {
        padding-bottom: 1.1mm;
        border-bottom: 1px dashed #666666;
        flex-shrink: 0;
        font-size: 8.5pt;
        line-height: 1.2;
    }

    .nota-root .doc-trx {
        font-size: 9.5pt;
        font-weight: 700;
        color: #000000;
    }

    /* 2. Customer + Date/Cashier block */
    .nota-root .customer-block {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 4mm;
        padding: 0.9mm 0;
        border-bottom: 1px dashed #666666;
        flex-shrink: 0;
        font-size: 8.5pt;
        line-height: 1.2;
    }

    .nota-root .customer-left {
        flex: 1 1 auto;
        min-width: 0;
    }

    .nota-root .customer-row {
        display: flex;
        gap: 1.5mm;
    }

    .nota-root .customer-row .label {
        flex: 0 0 17mm;
    }

    .nota-root .customer-row .value {
        flex: 1 1 auto;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nota-root .customer-right {
        flex: 0 0 auto;
        text-align: right;
        white-space: nowrap;
        line-height: 1.25;
    }

    /* 3. Item table (fixed 4 rows per sheet, no signatures) */
    .nota-root .items-section {
        padding: 0.6mm 0 0.8mm;
        border-bottom: 1px dashed #666666;
        flex-shrink: 0;
    }

    .nota-root table.items-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 9pt;
    }

    .nota-root table.items-table th {
        font-weight: 600;
        color: #222222;
        text-align: left;
        padding: 0.6mm 1.2mm 0.8mm;
        border-bottom: 1px solid #111111;
    }

    .nota-root table.items-table th.center,
    .nota-root table.items-table td.center {
        text-align: center;
    }

    .nota-root table.items-table th.num,
    .nota-root table.items-table td.num {
        text-align: right;
    }

    .nota-root table.items-table td {
        height: 5.4mm;
        padding: 0.4mm 1.2mm;
        vertical-align: middle;
        border-bottom: 1px dotted #bbbbbb;
        color: #111111;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .nota-root table.items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .nota-root .col-no { width: 8mm; }
    .nota-root .col-name { width: auto; }
    .nota-root .col-qty { width: 13mm; }
    .nota-root .col-price { width: 31mm; }
    .nota-root .col-total { width: 33mm; }

    .nota-root .item-disc-inline {
        font-size: 7.5pt;
        color: #dc2626;
        font-weight: 400;
        margin-left: 1.5mm;
    }

    /* 4. Payment & Totals only (clears bottom-right "HARGA SUDAH TERMASUK PPN") */
    .nota-root .bottom-section {
        padding-top: 1.4mm;
        flex-shrink: 0;
    }

    .nota-root .payment-totals {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 6mm;
    }

    .nota-root .payment-left {
        display: grid;
        grid-template-columns: 22mm minmax(26mm, auto);
        row-gap: 0.5mm;
        column-gap: 3mm;
        font-size: 9pt;
    }

    .nota-root .totals-right {
        display: grid;
        grid-template-columns: auto minmax(34mm, auto);
        row-gap: 0.45mm;
        column-gap: 4mm;
        align-items: baseline;
        text-align: right;
    }

    .nota-root .totals-right .subtotal-label {
        font-size: 9pt;
        color: #555555;
        font-weight: 400;
        text-align: left;
    }

    .nota-root .totals-right .subtotal-val {
        font-size: 9pt;
        color: #111111;
        font-weight: 500;
    }

    .nota-root .totals-right .extra-label {
        font-size: 8pt;
        color: #555555;
        font-weight: 400;
        text-align: left;
    }

    .nota-root .totals-right .extra-val {
        font-size: 8pt;
        color: #111111;
        font-weight: 500;
    }

    .nota-root .totals-right .extra-val.discount {
        color: #dc2626;
    }

    .nota-root .totals-right .grand-label {
        font-size: 11pt;
        font-weight: 700;
        color: #000000;
        text-align: left;
    }

    .nota-root .totals-right .grand-val {
        font-size: 14pt;
        font-weight: 700;
        color: #000000;
        line-height: 1.1;
    }

    /* Compact spacing when extra discount / voucher / PPN rows are present so totals clear pp-ppn */
    .nota-root .sheet-inner.has-extra-totals table.items-table td {
        height: 4.7mm;
        padding: 0.3mm 1.2mm;
    }

    .nota-root .sheet-inner.has-extra-totals .totals-right {
        row-gap: 0.2mm;
    }

    .nota-root .sheet-inner.has-extra-totals .totals-right .extra-label,
    .nota-root .sheet-inner.has-extra-totals .totals-right .extra-val {
        font-size: 7.5pt;
        line-height: 1.1;
    }
</style>
