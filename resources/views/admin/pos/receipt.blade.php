<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: "Courier New", "OCR A Std", monospace;
            font-size: 14px;
            width: 280px; /* Standard 80mm thermal printer width */
            margin: 0 auto;
            padding: 0 4px;
            font-weight: 600;
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .left { text-align: left; }
        .right { text-align: right; }
        .center { text-align: center; }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .line-double {
            border-top: 2px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td, table th {
            padding: 3px 0;
            vertical-align: top;
        }

        h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        p {
            margin: 4px 0;
            line-height: 1.4;
        }

        .text-muted {
            font-size: 12px;
            font-weight: 400;
            color: #333;
        }

        .bold { font-weight: 800; }
        .large { font-size: 16px; }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body, td, p, h3 {
                color: #000 !important;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    {{-- Store Header --}}
    <div class="center">
        <h3>LARA POS</h3>
        <p>House 45, Road 12, Dhanmondi<br>Dhaka 1209, Bangladesh</p>
        <p>Phone: +880 1712-345678</p>
        <p>Email: support@larapos.com.bd</p>
    </div>

    <div class="line-double"></div>

    {{-- Invoice Meta --}}
    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
        <span>INV: <span class="bold">{{ $sale->invoice_number }}</span></span>
        <span>{{ $sale->created_at->format('d/m/y h:ia') }}</span>
    </div>

    @if($sale->customer)
    <p>
        Customer: {{ $sale->customer->name }}<br>
        Phone: {{ $sale->customer->phone }}
    </p>
    @else
    <p>Customer: Walk-in Guest</p>
    @endif

    @if($sale->user)
    <p class="text-muted">Cashier: {{ $sale->user->name }}</p>
    @endif

    <div class="line"></div>

    {{-- Items Table --}}
    <table>
        <thead>
            <tr>
                <th class="left" style="width: 50%">Item</th>
                <th class="center" style="width: 15%">Qty</th>
                <th class="right" style="width: 35%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
            <tr>
                <td class="left">
                    <div>{{ $item->product_name }}</div>
                    @if (!empty($item->product_variant_id) || !empty($item->size_name) || !empty($item->color_name))
                    <div class="text-muted">
                        {{ trim(($item->size_name ?? '') . ' - ' . ($item->color_name ?? ''), ' -') }}
                    </div>
                    @endif
                </td>
                <td class="center">{{ number_format($item->quantity, 2) }}</td>
                <td class="right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    {{-- Totals Table --}}
    <table>
        <tr>
            <td class="left">SUBTOTAL</td>
            <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>

        @if ($sale->discount_amount > 0)
        <tr>
            <td class="left">DISCOUNT</td>
            <td class="right">- {{ number_format($sale->discount_amount, 2) }}</td>
        </tr>
        @endif

        @if ($sale->tax_amount > 0)
        <tr>
            <td class="left">TAX ({{ $sale->vat_rate }}%)</td>
            <td class="right">{{ number_format($sale->tax_amount, 2) }}</td>
        </tr>
        @endif

        <tr class="line-double">
            <td colspan="2"></td>
        </tr>

        <tr class="bold large">
            <td class="left">TOTAL AMOUNT</td>
            <td class="right">{{ number_format($sale->total_amount, 2) }}</td>
        </tr>

        <tr>
            <td class="left">PAID AMOUNT</td>
            <td class="right">{{ number_format($sale->paid_amount, 2) }}</td>
        </tr>

        <tr>
            <td class="left">CHANGE</td>
            <td class="right">{{ number_format($sale->change_amount, 2) }}</td>
        </tr>

        @if ($sale->due_amount > 0)
        <tr class="line">
            <td colspan="2"></td>
        </tr>
        <tr class="bold" style="color: #000;">
            <td class="left">AMOUNT DUE</td>
            <td class="right">{{ number_format($sale->due_amount, 2) }}</td>
        </tr>
        @endif
    </table>

    <div class="line-double"></div>

    {{-- Footer --}}
    <div class="center">
        <p class="bold">Thank you for shopping with us!</p>
        <p class="text-muted">Goods once sold will not be taken back<br>or exchanged without the original receipt.</p>
        <br>
        <p class="text-muted">*** END OF RECEIPT ***</p>
    </div>

    {{-- Auto-print script (optional, remove if not needed) --}}
    <script>
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
