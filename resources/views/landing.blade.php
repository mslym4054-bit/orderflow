<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OrderFlow — نظّم طلبات متجرك على انستغرام وواتساب</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base text-ink">

    <nav class="border-b border-line">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-neon" viewBox="0 0 32 32" fill="none">
                    <path d="M4 22c4 0 4-6 8-6s4 6 8 6 4-6 8-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 10l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-bold text-lg">OrderFlow</span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('about') }}" class="text-muted hover:text-ink">من نحن</a>
                <a href="{{ route('login') }}" class="text-muted hover:text-ink">دخول</a>
                <a href="{{ route('register') }}" class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold">ابدأ مجانًا</a>
            </div>
        </div>
    </nav>

    <section class="max-w-4xl mx-auto px-6 py-24 text-center">
        <h1 class="text-3xl sm:text-5xl font-black leading-tight">
            وداعًا لفوضى الطلبات على <span class="text-neon">واتساب وانستغرام</span>
        </h1>
        <p class="mt-6 text-muted text-lg max-w-2xl mx-auto">
            نظّم طلبات زبائنك، تابع الأرباح والخسائر، واعرف أداء متجرك بنظرة واحدة — بدون تعقيد، بدون تكلفة تأسيس عالية.
        </p>
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('register') }}" class="bg-neon hover:bg-neon-dim text-night px-6 py-3 rounded font-semibold">ابدأ الآن مجانًا</a>
            <a href="{{ route('about') }}" class="border border-line hover:border-neon/40 px-6 py-3 rounded font-semibold text-ink">تعرف أكثر</a>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-6 pb-24 grid sm:grid-cols-3 gap-6">
        <div class="bg-surface border border-line rounded p-6">
            <svg class="w-8 h-8 text-neon mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/></svg>
            <h3 class="font-semibold mb-1">إدارة طلبات منظمة</h3>
            <p class="text-sm text-muted">عملاء، طلبات، وحالات واضحة بدل آلاف الرسائل المتناثرة.</p>
        </div>
        <div class="bg-surface border border-line rounded p-6">
            <svg class="w-8 h-8 text-neon mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V10m5 9V5m5 14v-7m5 7V3"/></svg>
            <h3 class="font-semibold mb-1">أرباح وخسائر فورية</h3>
            <p class="text-sm text-muted">اعرف صافي ربحك لحظة بلحظة، بدون إكسل ولا حسابات يدوية.</p>
        </div>
        <div class="bg-surface border border-line rounded p-6">
            <svg class="w-8 h-8 text-neon mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z"/></svg>
            <h3 class="font-semibold mb-1">تواصل مباشر بواتساب</h3>
            <p class="text-sm text-muted">تواصل مع عملائك بضغطة واحدة من داخل لوحة التحكم.</p>
        </div>
    </section>

    <footer class="border-t border-line py-8 text-center text-sm text-muted">
        <a href="{{ route('about') }}" class="hover:text-ink">من نحن وتواصل معنا</a>
        — OrderFlow {{ date('Y') }}
    </footer>

</body>
</html>
