<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>من نحن — OrderFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base text-ink">

    <nav class="border-b border-line">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <svg class="w-8 h-8 text-neon" viewBox="0 0 32 32" fill="none">
                    <path d="M4 22c4 0 4-6 8-6s4 6 8 6 4-6 8-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 10l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-bold text-lg">OrderFlow</span>
            </a>
            <a href="{{ route('login') }}" class="text-sm text-muted hover:text-ink">دخول</a>
        </div>
    </nav>

    <section class="max-w-2xl mx-auto px-6 py-16">
        <h1 class="text-2xl font-bold mb-4">عن OrderFlow</h1>
        <p class="text-muted leading-relaxed mb-10">
            OrderFlow أداة بسيطة صممتها لحل مشكلة واجهتها كتير من تجار انستغرام وواتساب: فوضى إدارة الطلبات يدويًا عبر الشات.
            هدفها تنظيم الطلبات والعملاء، ومتابعة الأرباح والخسائر بسهولة، بدون تعقيد أنظمة المتاجر الإلكترونية الكبيرة.
        </p>

        <h2 class="text-lg font-semibold mb-4">تواصل معنا</h2>

        @if (session('status'))
            <div class="mb-4 p-3 bg-neon/10 text-neon rounded border border-neon/20">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('feedback.store') }}" class="space-y-4 bg-surface border border-line rounded p-6">
            @csrf
            <div class="grid sm:grid-cols-2 gap-3">
                <input type="text" name="name" placeholder="اسمك (اختياري)" class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                <input type="email" name="email" placeholder="بريدك (اختياري)" class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
            </div>
            <textarea name="message" required rows="4" placeholder="رسالتك أو ملاحظتك..." class="w-full bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon"></textarea>
            <button class="bg-neon hover:bg-neon-dim text-night px-5 py-2 rounded font-semibold">إرسال</button>
        </form>
    </section>

</body>
</html>
