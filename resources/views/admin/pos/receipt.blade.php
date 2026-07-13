<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order #{{ $order->order_number }} Receipt</title>
    <style>
        body {
            font-family: "Courier New", "OCR A Std", monospace;
            /* font-size: 13px; */
            font-size: 16px;
            width: 280px;
            margin: 0 auto;
            padding: 0 4px;
            font-weight: 600;
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .left {
            text-align: start;
        }

        .right {
            text-align: end;
        }

        .center {
            text-align: center;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 2px 0;
            /* font-weight: 600; */
        }

        h3 {
            margin-bottom: 5px;
        }

        p {
            margin-top: 5px;
            margin-bottom: 8px;
        }

        @media print {
            @page {
                margin: 0;
            }

            body,
            td,
            p,
            h3 {
                color: #000 !important;
                font-weight: 600 !important;
            }

            .bold {
                font-weight: 700 !important;
            }
        }
    </style>

</head>

<?php
$showCurrency = false;
?>

<body>
    <div class="center">
        <h3 class="bold">{{ $siteName }}</h3>
        <p>123 Fashion Street, Dhaka 1215, Bangladesh</p>
        <p> Phone: +880 1711-123456</p>
    </div>

    <div class="line" style="margin-top: 20px;"></div>

    <p>
    <div style="display: flex; justify-content: space-between; margin-bottom:10px;">
        <span>#{{ $order->order_number }}</span>
        <span>{{ $order->created_at->format('d/m/y h:ia') }}</span>
    </div>

    @if (!is_null($order->customer_id))
    Customer: {{ $order->customer->name }}<br>
    Phone: {{ $order->customer->phone }}
    @endif
    </p>

    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th class="left" style="width: 65%">Item</th>
                <th class="right" style="width: 35%">Total</th>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="line"></div>
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
            <tr>
                <td class="left">
                    <div>{{ $item->product_name }} @if($item->quantity > 1) {{ $item->quantity }}@endif</div>

                    @if ($item->variant)
                    <small class="text-muted d-block">
                        {{ $item->size_name }} - {{ $item->color_name }}
                    </small>
                    @endif
                </td>

                <td class="right">
                    {{ money($item->unit_price * $item->quantity, $showCurrency) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td class="left">SUBTOTAL</td>
            <td class="right">{{ money($order->subtotal, $showCurrency) }}</td>
        </tr>
        @if ($order->discount > 0)
        <tr>
            <td class="left">DISCOUNT</td>
            <td class="right">{{ money($order->discount, $showCurrency) }}</td>
        </tr>
        @endif
        <!-- <tr>
            <td colspan="2">
                <div class="line"></div>
            </td>
        </tr> -->
        <tr class="totals">
            <td class="left"><strong>TOTAL</strong></td>
            <td class="right"><strong>{{ money($order->total, $showCurrency) }}</strong></td>
        </tr>
        <tr class="totals">
            <td class="left"><strong>CASH RECEIVED</strong></td>
            <td class="right"><strong>{{ money($order->cash_received, $showCurrency) }}</strong></td>
        </tr>
        <tr class="totals">
            <td class="left"><strong>CASH RETURNED</strong></td>
            <td class="right"><strong>{{ money($order->cash_returned, $showCurrency) }}</strong></td>
        </tr>
    </table>

    @if ($order->due > 0)
    <div class="line"></div>
    <table>
        <tr>
            <td class="left">Amount Paid</td>
            <td class="right">{{ money($order->paid, $showCurrency) }}</td>
        </tr>
        <tr>
            <td class="left"><strong>Amount Due</strong></td>
            <td class="right"><strong>{{ money($order->due, $showCurrency) }}</strong></td>
        </tr>
    </table>
    @endif

    <div class="line"></div>

    <div class="center">
        <p style="margin-top: 20px;">Thank you for shopping with us!</p>
        <p>www.slashfasion.com</p>
    </div>
</body>

</html>