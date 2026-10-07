
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>فاتورة طلب #{{ $order->id }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #0B0F12;
            color: #E7EDEC;
            margin: 0;
            padding: 40px 20px;
        }

        .invoice {
            max-width: 760px;
            margin: auto;
            background: #151B20;
            border: 1px solid #262E35;
            border-radius: 14px;
            overflow: hidden;
        }

        .header {
            padding: 28px 32px;
            border-bottom: 1px solid #262E35;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
        }

        .brand {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .subtitle {
            color: #8B98A0;
            font-size: 0.9rem;
            margin-top: 4px;
        }

        .invoice-number {
            text-align: left;
            font-size: 0.95rem;
        }

        .muted {
            color: #8B98A0;
        }

        .content {
            padding: 28px 32px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 28px;
        }

        .info-title {
            color: #8B98A0;
            font-size: 0.82rem;
            margin-bottom: 6px;
        }

        .info-value {
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 10px;
            text-align: right;
            border-bottom: 1px solid #262E35;
        }

        th {
            color: #8B98A0;
            font-size: 0.85rem;
            font-weight: 500;
        }

        td {
            font-size: 0.92rem;
        }

        .product-name {
            font-weight: 500;
        }

        .summary {
            margin-top: 24px;
            margin-right: auto;
            width: 300px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 8px 0;
            color: #8B98A0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 10px;
            padding-top: 14px;
            border-top: 1px solid #262E35;
            font-size: 1.25rem;
            font-weight: 700;
            color: #39FFC2;
        }

        .notes {
            margin-top: 28px;
            padding: 14px 16px;
            background: #101519;
            border: 1px solid #262E35;
            border-radius: 8px;
        }

        .notes-title {
            color: #8B98A0;
            font-size: 0.82rem;
            margin-bottom: 5px;
        }

        .actions {
            padding: 22px 32px;
            border-top: 1px solid #262E35;
            text-align: center;
        }

        .btn {
            background: #39FFC2;
            color: #0B0F12;
            border: none;
            padding: 11px 22px;
            border-radius: 7px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .header,
            .content {
                padding: 22px 18px;
            }

            .header-row {
                flex-direction: column;
            }

            .invoice-number {
                text-align: right;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .summary {
                width: 100%;
            }

            th,
            td {
                padding: 10px 6px;
                font-size: 0.82rem;
            }
        }

        @media print {
            body {
                background: #fff;
                color: #000;
                padding: 0;
            }

            .invoice {
                max-width: none;
                border: none;
                border-radius: 0;
                background: #fff;
            }

            .header,
            .content,
            .actions {
                background: #fff;
            }

            th,
            td {
                border-bottom: 1px solid #ccc;
            }

            .summary-total {
                border-top: 1px solid #ccc;
                color: #000;
            }

            .notes {
                background: #fff;
                border: 1px solid #ccc;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

@php
    /*
     * الطلبات الجديدة:
     * نستخدم order_items.
     *
     * الطلبات القديمة:
     * نحافظ على التوافق مع الحقول القديمة داخل orders.
     */

    $items = $order->items;

    if ($items->isEmpty()) {
        $items = collect([
            (object) [
                'item' => $order->item,
                'quantity' => $order->quantity,
                'price' => $order->price,
            ],
        ]);
    }

    $total = $items->sum(function ($item) {
        return ($item->price ?? 0) * ($item->quantity ?? 0);
    });
@endphp

<div class="invoice">

    {{-- Header --}}
    <div class="header">

        <div class="header-row">

            <div>
                <div class="brand">
                    OrderFlow
                </div>

                <div class="subtitle">
                    فاتورة مبيعات
                </div>
            </div>

            <div class="invoice-number">

                <div>
                    فاتورة رقم:
                    <strong>#{{ $order->id }}</strong>
                </div>

                <div class="muted" style="margin-top: 5px;">
                    {{ $order->created_at->format('Y-m-d') }}
                </div>

            </div>

        </div>

    </div>

    {{-- Content --}}
    <div class="content">

        {{-- Customer Information --}}
        <div class="info-grid">

            <div>
                <div class="info-title">
                    البائع
                </div>

                <div class="info-value">
                    {{ $order->user->name ?? auth()->user()->name }}
                </div>
            </div>

            <div>
                <div class="info-title">
                    العميل
                </div>

                <div class="info-value">
                    {{ $order->customer->name ?? '-' }}
                </div>

                @if ($order->customer?->phone)

                    <div
                        class="muted"
                        style="font-size: 0.85rem; margin-top: 4px;"
                    >
                        {{ $order->customer->phone }}
                    </div>

                @endif
            </div>

        </div>

        {{-- Products --}}
        <table>

            <thead>

                <tr>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($items as $item)

                    @php
                        $itemTotal =
                            ($item->price ?? 0) *
                            ($item->quantity ?? 0);
                    @endphp

                    <tr>

                        <td class="product-name">
                            {{ $item->item }}
                        </td>

                        <td>
                            {{ $item->quantity }}
                        </td>

                        <td>
                            {{ number_format($item->price ?? 0, 2) }}
                        </td>

                        <td>
                            {{ number_format($itemTotal, 2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        {{-- Total --}}
        <div class="summary">

            <div class="summary-total">

                <span>
                    الإجمالي
                </span>

                <span>
                    {{ number_format($total, 2) }}
                </span>

            </div>

        </div>

        {{-- Notes --}}
        @if ($order->notes)

            <div class="notes">

                <div class="notes-title">
                    ملاحظات
                </div>

                <div>
                    {{ $order->notes }}
                </div>

            </div>

        @endif

    </div>

    {{-- Actions --}}
    <div class="actions no-print">

        <button
            class="btn"
            onclick="window.print()"
        >
            طباعة / حفظ PDF
        </button>

    </div>

</div>

</body>
</html>
```
