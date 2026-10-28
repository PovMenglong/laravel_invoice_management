@php
    $c = config('company');
    $money = fn($n) => $c['currency'] . number_format($n, 2);
    $qty = fn($n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    $watermark = ['draft' => 'DRAFT', 'void' => 'VOID'][$invoice->status] ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            color: #111;
            font-size: 13px;
            font-family: "Segoe UI", "Khmer UI", "Noto Sans Khmer", Arial, sans-serif;
        }

        .toolbar {
            max-width: 210mm;
            margin: 12px auto;
            display: flex;
            gap: 8px;
        }

        .toolbar button,
        .toolbar a {
            padding: 8px 16px;
            border: 1px solid #9ca3af;
            border-radius: 6px;
            background: #fff;
            color: #111;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }

        .sheet {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            padding: 15mm;
            background: #fff;
            box-shadow: 0 0 6px rgba(0, 0, 0, .15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111;
            padding-bottom: 12px;
        }

        .header img {
            max-height: 60px;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 700;
        }

        .muted {
            color: #555;
        }

        .title {
            text-align: right;
        }

        .title h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 2px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
        }

        .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #777;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.items th {
            background: #f1f1f1;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #999;
        }

        table.items td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .num {
            text-align: right;
        }

        thead {
            display: table-header-group;
        }

        /* repeat header on every page */
        tr {
            break-inside: avoid;
        }

        /* never split a row across pages */

        .totals {
            width: 260px;
            margin: 12px 0 0 auto;
        }

        .totals div {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }

        .totals .grand {
            border-top: 2px solid #111;
            font-size: 16px;
            font-weight: 700;
        }

        .notes {
            margin-top: 24px;
            white-space: pre-line;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 70px;
            break-inside: avoid;
        }

        .sig {
            width: 40%;
            text-align: center;
            border-top: 1px solid #111;
            padding-top: 6px;
        }

        .watermark {
            position: fixed;
            top: 38%;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 120px;
            font-weight: 800;
            color: rgba(0, 0, 0, .07);
            transform: rotate(-30deg);
            pointer-events: none;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .sheet {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar no-print">
        <button type="button" onclick="window.print()">Print</button>
        <a href="{{ route('invoices.show', $invoice) }}">Back</a>
    </div>

    @if ($watermark)
        <div class="watermark">{{ $watermark }}</div>
    @endif

    <div class="sheet">
        <div class="header">
            <div>
                @if (file_exists(public_path('logo.png')))
                    <img src="{{ asset('logo.png') }}" alt="">
                @endif
                <div class="company-name">{{ $c['name'] }}</div>
                <div class="muted">{{ $c['address'] }}</div>
                <div class="muted">{{ $c['phone'] }} {{ $c['email'] ? '· ' . $c['email'] : '' }}</div>
            </div>
            <div class="title">
                <h1>INVOICE</h1>
                <div><strong>{{ $invoice->number }}</strong></div>
            </div>
        </div>

        <div class="meta">
            <div>
                <div class="label">Bill to</div>
                <div><strong>{{ $invoice->customer->name }}</strong></div>
                <div class="muted" style="white-space: pre-line">{{ $invoice->customer->address }}</div>
                <div class="muted">{{ $invoice->customer->phone }}</div>
            </div>
            <div style="text-align: right">
                <div><span class="label">Issue date</span> {{ $invoice->issue_date->format('d M Y') }}</div>
                <div><span class="label">Due date</span> {{ $invoice->due_date?->format('d M Y') ?? '-' }}</div>
                <div><span class="label">Status</span> {{ ucfirst($invoice->status) }}</div>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 32px">#</th>
                    <th>Description</th>
                    <th class="num" style="width: 70px">Qty</th>
                    <th class="num" style="width: 110px">Unit price</th>
                    <th class="num" style="width: 110px">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="num">{{ $qty($item->qty) }}</td>
                        <td class="num">{{ $money($item->unit_price) }}</td>
                        <td class="num">{{ $money($item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div><span>Subtotal</span><span>{{ $money($invoice->subtotal) }}</span></div>
            @if ($invoice->discount_amount > 0)
                <div>
                    <span>Discount{{ $invoice->discount_type === 'percent' ? ' (' . rtrim(rtrim($invoice->discount_value, '0'), '.') . '%)' : '' }}</span>
                    <span>-{{ $money($invoice->discount_amount) }}</span>
                </div>
            @endif
            @if ($invoice->tax_rate > 0)
                <div><span>Tax
                        ({{ rtrim(rtrim($invoice->tax_rate, '0'), '.') }}%)</span><span>{{ $money($invoice->tax_amount) }}</span>
                </div>
            @endif
            <div class="grand"><span>Total</span><span>{{ $money($invoice->total) }}</span></div>
        </div>

        @if ($invoice->notes)
            <div class="notes"><span class="label">Notes</span><br>{{ $invoice->notes }}</div>
        @endif

        <div class="signatures">
            <div class="sig">Customer signature</div>
            <div class="sig">Authorized signature</div>
        </div>
    </div>

</body>

</html>
