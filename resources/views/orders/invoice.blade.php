<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>فاتورة طلب #{{ $order->id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #0B0F12; color: #E7EDEC; margin: 0; padding: 40px; }
        .box { max-width: 650px; margin: auto; background: #151B20; border: 1px solid #262E35; border-radius: 8px; padding: 32px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: right; padding: 10px; border-bottom: 1px solid #262E35; }
        th { color: #8B98A0; font-weight: 500; }
        .total { font-size: 1.3rem; font-weight: bold; color: #39FFC2; }
        .btn { background: #39FFC2; color: #0B0F12; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; font-family: inherit; }
        @media print {
            body { background: #fff; color: #000; }
            .box { background: #fff; border: none; }
            th, td { border-bottom: 1px solid #ccc; }
            .total { color: #000; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="row">
            <div>
                <strong style="font-size:1.3rem;">OrderFlow</strong>
                <div style="color:#8B98A0; font-size:0.9rem;">فاتورة مبيعات</div>
            </div>
            <div style="text-align:left;">
                <div>فاتورة رقم: #{{ $order->id }}</div>
                <div style="color:#8B98A0; font-size:0.9rem;">{{ $order->created_at->format('Y-m-d') }}</div>
            </div>
        </div>

        <hr style="border-color:#262E35; margin:20px 0;">

        <div class="row">
            <div>
                <div style="color:#8B98A0; font-size:0.85rem;">البائع</div>
                <div>{{ $order->user->name ?? auth()->user()->name }}</div>
            </div>
            <div style="text-align:left;">
                <div style="color:#8B98A0; font-size:0.85rem;">العميل</div>
                <div>{{ $order->customer->name }}</div>
                <div style="color:#8B98A0; font-size:0.85rem;">{{ $order->customer->phone }}</div>
            </div>
        </div>

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
                <tr>
                    <td>{{ $order->item }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>{{ number_format($order->price, 2) }}</td>
                    <td>{{ number_format($order->price * $order->quantity, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="row" style="margin-top:20px; justify-content:flex-start; gap:40px;">
            <div></div>
        </div>
        <div style="text-align:left; margin-top:10px;">
            <span class="total">الإجمالي: {{ number_format($order->price * $order->quantity, 2) }}</span>
        </div>

        @if ($order->notes)
            <div style="margin-top:20px; color:#8B98A0; font-size:0.9rem;">ملاحظات: {{ $order->notes }}</div>
        @endif

        <div class="no-print" style="margin-top:30px; text-align:center;">
            <button class="btn" onclick="window.print()">طباعة / حفظ PDF</button>
        </div>
    </div>
</body>
</html>
