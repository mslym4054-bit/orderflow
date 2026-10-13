
<!DOCTYPE html>
<html lang="{{ auth()->user()?->locale ?? 'ar' }}"
      dir="{{ (auth()->user()?->locale ?? 'ar') === 'ar' ? 'rtl' : 'ltr' }}"
      data-theme="{{ auth()->user()?->theme ?? 'light' }}"
      data-brand-color="{{ auth()->user()?->brand_color ?? 'green' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OrderFlow') }}</title>

    <script>
        (() => {
            const savedTheme = @json(auth()->user()?->theme ?? 'light');

            document.documentElement.dataset.theme =
                savedTheme === 'dark' ? 'dark' : 'light';
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ========================================
           المتغيرات الأساسية - الوضع الفاتح
        ======================================== */

        :root {
            color-scheme: light;

            --page: #f4f7f6;
            --surface: #ffffff;
            --surface-hover: #eef3f1;
            --line: #e0e8e4;
            --ink: #17251f;
            --muted: #718078;

            --accent: #10b981;
            --accent-soft: #d1fae5;
            --accent-hover: #059669;
            --accent-contrast: #ffffff;

            --sidebar: #ffffff;
            --sidebar-text: #17251f;
            --sidebar-muted: #718078;
            --sidebar-border: #e0e8e4;
            --sidebar-hover: #e6f7ef;
            --sidebar-active: #d1fae5;
            --sidebar-active-border: #6ee7b7;
            --sidebar-active-text: #047857;

            --shadow: 0 4px 18px rgba(25, 50, 39, .05);
        }

        /* ========================================
           الوضع الداكن
        ======================================== */

        html[data-theme="dark"] {
            color-scheme: dark;

            --page: #0d1311;
            --surface: #151e1a;
            --surface-hover: #202c26;
            --line: #2a3831;
            --ink: #e6eee9;
            --muted: #9aaba1;

            --accent: #39d9a0;
            --accent-soft: #173c2d;
            --accent-hover: #2bc48e;
            --accent-contrast: #08130e;

            --sidebar: #080e0b;
            --sidebar-text: #e0ebe4;
            --sidebar-muted: #91a49a;
            --sidebar-border: #2a3831;
            --sidebar-hover: #172820;
            --sidebar-active: #173c2d;
            --sidebar-active-border: #286b4e;
            --sidebar-active-text: #39d9a0;

            --shadow: 0 4px 18px rgba(0, 0, 0, .15);
        }

        /* ========================================
           ألوان الهوية
        ======================================== */

        html[data-brand-color="green"] {
            --accent: #10b981;
            --accent-soft: #d1fae5;
            --accent-hover: #059669;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #047857;
            --sidebar-active-border: #6ee7b7;
        }

        html[data-brand-color="blue"] {
            --accent: #3b82f6;
            --accent-soft: #dbeafe;
            --accent-hover: #2563eb;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #1d4ed8;
            --sidebar-active-border: #93c5fd;
        }

        html[data-brand-color="purple"] {
            --accent: #8b5cf6;
            --accent-soft: #ede9fe;
            --accent-hover: #7c3aed;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #6d28d9;
            --sidebar-active-border: #c4b5fd;
        }

        html[data-brand-color="orange"] {
            --accent: #f97316;
            --accent-soft: #ffedd5;
            --accent-hover: #ea580c;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #c2410c;
            --sidebar-active-border: #fdba74;
        }

        html[data-brand-color="rose"] {
            --accent: #f43f5e;
            --accent-soft: #ffe4e6;
            --accent-hover: #e11d48;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #be123c;
            --sidebar-active-border: #fda4af;
        }

        html[data-brand-color="teal"] {
            --accent: #14b8a6;
            --accent-soft: #ccfbf1;
            --accent-hover: #0d9488;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #0f766e;
            --sidebar-active-border: #5eead4;
        }

        html[data-brand-color="indigo"] {
            --accent: #6366f1;
            --accent-soft: #e0e7ff;
            --accent-hover: #4f46e5;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #4338ca;
            --sidebar-active-border: #a5b4fc;
        }

        html[data-brand-color="amber"] {
            --accent: #f59e0b;
            --accent-soft: #fef3c7;
            --accent-hover: #d97706;
            --sidebar-active: var(--accent-soft);
            --sidebar-active-text: #92400e;
            --sidebar-active-border: #fcd34d;
        }

        /* تحسين التباين لألوان الهوية في الوضع الداكن */

        html[data-theme="dark"][data-brand-color="green"] {
            --accent-soft: #173c2d;
            --sidebar-active: #173c2d;
            --sidebar-active-text: #6ee7b7;
            --sidebar-active-border: #34d399;
        }

        html[data-theme="dark"][data-brand-color="blue"] {
            --accent-soft: #172c4b;
            --sidebar-active: #172c4b;
            --sidebar-active-text: #93c5fd;
            --sidebar-active-border: #3b82f6;
        }

        html[data-theme="dark"][data-brand-color="purple"] {
            --accent-soft: #30204d;
            --sidebar-active: #30204d;
            --sidebar-active-text: #c4b5fd;
            --sidebar-active-border: #8b5cf6;
        }

        html[data-theme="dark"][data-brand-color="orange"] {
            --accent-soft: #492b19;
            --sidebar-active: #492b19;
            --sidebar-active-text: #fdba74;
            --sidebar-active-border: #f97316;
        }

        html[data-theme="dark"][data-brand-color="rose"] {
            --accent-soft: #491e2b;
            --sidebar-active: #491e2b;
            --sidebar-active-text: #fda4af;
            --sidebar-active-border: #f43f5e;
        }

        html[data-theme="dark"][data-brand-color="teal"] {
            --accent-soft: #123b37;
            --sidebar-active: #123b37;
            --sidebar-active-text: #5eead4;
            --sidebar-active-border: #14b8a6;
        }

        html[data-theme="dark"][data-brand-color="indigo"] {
            --accent-soft: #252650;
            --sidebar-active: #252650;
            --sidebar-active-text: #a5b4fc;
            --sidebar-active-border: #6366f1;
        }

        html[data-theme="dark"][data-brand-color="amber"] {
            --accent-soft: #45351a;
            --sidebar-active: #45351a;
            --sidebar-active-text: #fcd34d;
            --sidebar-active-border: #f59e0b;
        }

        /* ========================================
           الأساسيات والخلفية العامة
        ======================================== */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            background-color: var(--page);
            color: var(--ink);
            font-family: Tajawal, "Segoe UI", Tahoma, Arial, sans-serif;
            transition: background-color .2s ease, color .2s ease;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .of-shell {
            min-height: 100vh;
            background-color: var(--page);
            color: var(--ink);
            accent-color: var(--accent);
        }


.of-main {
    min-height: 100vh;
    margin-inline-start: 260px;
    background-color: var(--page);
    color: var(--ink);
}


        .of-content {
            min-width: 0;
            padding: 28px;
            background-color: var(--page);
            color: var(--ink);
        }

        .of-topbar {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 28px;
            background-color: var(--surface);
            color: var(--ink);
            border-bottom: 1px solid var(--line);
        }

        /* ========================================
           القائمة الجانبية
        ======================================== */

        .of-sidebar {
            position: fixed;
            inset-block: 0;
            inset-inline-start: 0;
            width: 260px;
            overflow-y: auto;
            background-color: var(--sidebar) !important;
            color: var(--sidebar-text) !important;
            z-index: 50;
            border-inline-end: 1px solid var(--sidebar-border);
        }

        .of-navigation {
            background-color: var(--sidebar) !important;
            color: var(--sidebar-text) !important;
        }

        .of-navigation .of-brand-link,
        .of-navigation .of-account-name {
            color: var(--sidebar-text);
        }

        .of-navigation .of-brand-caption,
        .of-navigation .of-account-email,
        .of-navigation .of-nav-heading {
            color: var(--sidebar-muted);
        }

        .of-navigation .of-nav-link {
            color: var(--sidebar-text);
            border: 1px solid transparent;
            border-radius: 10px;
            transition: background-color .18s ease, color .18s ease,
                        border-color .18s ease;
        }

        .of-navigation .of-nav-link:hover {
    background-color: var(--sidebar-hover) !important;
    color: var(--sidebar-active-text) !important;
    border-color: var(--sidebar-active-border);
}

.of-navigation .of-nav-link:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
}

.of-navigation .of-nav-link.active {
    background-color: var(--sidebar-active) !important;
    color: var(--sidebar-active-text) !important;
    border-color: var(--sidebar-active-border) !important;
    font-weight: 700;
}
        .of-navigation .of-nav-link.active svg {
            color: var(--sidebar-active-text);
        }

        /* ========================================
           أزرار المظهر والقائمة
        ======================================== */

        .of-theme-button,
        .of-menu-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background-color: var(--surface);
            color: var(--ink);
            cursor: pointer;
            white-space: nowrap;
            transition: background-color .2s ease, border-color .2s ease,
                        color .2s ease;
        }

        .of-theme-button:hover,
        .of-menu-button:hover {
            background-color: var(--surface-hover);
        }

        .of-menu-button {
            display: none;
        }

        .of-overlay {
            display: none;
        }

        /* ========================================
           الخلفيات والنصوص والحدود
        ======================================== */

        .of-shell .bg-base,
        .of-shell .bg-gray-50,
        .of-shell .bg-gray-100 {
            background-color: var(--page) !important;
        }

        .of-shell .bg-white,
        .of-shell .bg-surface {
            background-color: var(--surface) !important;
        }

        .of-shell .bg-surface2 {
            background-color: var(--surface-hover) !important;
        }

        .of-shell .text-ink,
        .of-shell .text-gray-900,
        .of-shell .text-gray-800,
        .of-shell .text-gray-700 {
            color: var(--ink) !important;
        }

        .of-shell .text-muted,
        .of-shell .text-gray-600,
        .of-shell .text-gray-500,
        .of-shell .text-gray-400 {
            color: var(--muted) !important;
        }

        .of-shell .border-line,
        .of-shell .border-gray-100,
        .of-shell .border-gray-200,
        .of-shell .border-gray-300 {
            border-color: var(--line) !important;
        }

        .of-shell input:not([type="checkbox"]):not([type="radio"]),
        .of-shell select,
        .of-shell textarea {
            background-color: var(--surface);
            color: var(--ink);
            border-color: var(--line);
        }

        .of-shell input::placeholder,
        .of-shell textarea::placeholder {
            color: var(--muted);
        }

        .of-shell table {
            color: var(--ink);
        }

        .of-shell th,
        .of-shell td {
            border-color: var(--line);
        }

        .of-shell .shadow,
        .of-shell .shadow-sm,
        .of-shell .shadow-md,
        .of-shell .shadow-lg {
            box-shadow: var(--shadow);
        }

        /* ========================================
           الأزرار الأساسية ولون الهوية
        ======================================== */

        .of-shell .of-primary-button {
            background-color: var(--accent);
            color: var(--accent-contrast);
            border-color: var(--accent);
        }

        .of-shell .bg-neon {
            background-color: var(--accent) !important;
            color: var(--accent-contrast) !important;
            border-color: var(--accent) !important;
        }

        .of-shell .hover\:bg-neon-dim:hover {
            background-color: var(--accent-hover) !important;
            border-color: var(--accent-hover) !important;
            color: var(--accent-contrast) !important;
        }

        .of-shell .bg-emerald-600,
        .of-shell .bg-emerald-500,
        .of-shell .bg-green-600,
        .of-shell .bg-green-500,
        .of-shell .bg-blue-600,
        .of-shell .bg-blue-500,
        .of-shell .bg-purple-600,
        .of-shell .bg-purple-500,
        .of-shell .bg-indigo-600,
        .of-shell .bg-indigo-500,
        .of-shell .bg-orange-600,
        .of-shell .bg-orange-500,
        .of-shell .bg-teal-600,
        .of-shell .bg-teal-500 {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
        }

        .of-shell .hover\:bg-emerald-700:hover,
        .of-shell .hover\:bg-emerald-600:hover,
        .of-shell .hover\:bg-green-700:hover,
        .of-shell .hover\:bg-green-600:hover,
        .of-shell .hover\:bg-blue-700:hover,
        .of-shell .hover\:bg-blue-600:hover,
        .of-shell .hover\:bg-purple-700:hover,
        .of-shell .hover\:bg-purple-600:hover,
        .of-shell .hover\:bg-indigo-700:hover,
        .of-shell .hover\:bg-indigo-600:hover,
        .of-shell .hover\:bg-orange-700:hover,
        .of-shell .hover\:bg-orange-600:hover,
        .of-shell .hover\:bg-teal-700:hover,
        .of-shell .hover\:bg-teal-600:hover {
            background-color: var(--accent-hover) !important;
            border-color: var(--accent-hover) !important;
        }

        .of-shell .focus\:ring-emerald-500:focus,
        .of-shell .focus\:ring-green-500:focus,
        .of-shell .focus\:ring-blue-500:focus,
        .of-shell .focus\:ring-purple-500:focus,
        .of-shell .focus\:ring-indigo-500:focus,
        .of-shell .focus\:ring-orange-500:focus,
        .of-shell .focus\:ring-rose-500:focus,
        .of-shell .focus\:ring-teal-500:focus,
        .of-shell .focus\:ring-amber-500:focus {
            --tw-ring-color: var(--accent) !important;
        }

        /* ========================================
           الروابط والأيقونات
        ======================================== */

        .of-shell a.text-emerald-600,
        .of-shell a.text-green-600,
        .of-shell a.text-blue-600,
        .of-shell a.text-purple-600,
        .of-shell a.text-indigo-600,
        .of-shell a.text-orange-600,
        .of-shell a.text-teal-600,
        .of-shell a.text-amber-600,
        .of-shell a.text-emerald-700,
        .of-shell a.text-green-700,
        .of-shell a.text-blue-700,
        .of-shell a.text-purple-700,
        .of-shell a.text-indigo-700,
        .of-shell a.text-orange-700,
        .of-shell a.text-teal-700,
        .of-shell a.text-amber-700 {
            color: var(--accent) !important;
        }

        .of-shell .text-emerald-600,
        .of-shell .text-green-600,
        .of-shell .text-blue-600,
        .of-shell .text-purple-600,
        .of-shell .text-indigo-600,
        .of-shell .text-orange-600,
        .of-shell .text-teal-600,
        .of-shell .text-amber-600,
        .of-shell .text-emerald-700,
        .of-shell .text-green-700,
        .of-shell .text-blue-700,
        .of-shell .text-purple-700,
        .of-shell .text-indigo-700,
        .of-shell .text-teal-700,
        .of-shell .text-amber-700 {
            color: var(--accent) !important;
        }

        .of-shell .border-emerald-500,
        .of-shell .border-green-500,
        .of-shell .border-blue-500,
        .of-shell .border-purple-500,
        .of-shell .border-indigo-500,
        .of-shell .border-orange-500,
        .of-shell .border-teal-500,
        .of-shell .border-amber-500 {
            border-color: var(--accent) !important;
        }

        .of-shell .bg-emerald-50,
        .of-shell .bg-green-50,
        .of-shell .bg-blue-50,
        .of-shell .bg-purple-50,
        .of-shell .bg-indigo-50,
        .of-shell .bg-orange-50,
        .of-shell .bg-teal-50,
        .of-shell .bg-amber-50,
        .of-shell .bg-neon\/10 {
            background-color: var(--accent-soft) !important;
        }

        .of-shell .border-neon\/20 {
            border-color: var(--accent) !important;
        }

        .of-shell .of-primary-icon,
        .of-shell .text-neon {
            color: var(--accent) !important;
        }

        /* ========================================
           العناوين والبطاقات
        ======================================== */

        .of-shell h1,
        .of-shell h2,
        .of-shell h3 {
            text-decoration-color: var(--accent);
            text-underline-offset: 6px;
        }

        .of-shell h1 {
            text-decoration-line: underline;
            text-decoration-thickness: 3px;
        }

        .of-shell .bg-white.shadow,
        .of-shell .bg-white.shadow-sm,
        .of-shell .bg-white.shadow-md,
        .of-shell .bg-surface.shadow,
        .of-shell .bg-surface.shadow-sm,
        .of-shell .bg-surface.shadow-md {
            border-top: 3px solid var(--accent);
        }

        .of-shell .ring-1,
        .of-shell .ring-2 {
            --tw-ring-color: var(--accent) !important;
        }

        .of-shell .bg-emerald-50 svg,
        .of-shell .bg-green-50 svg,
        .of-shell .bg-blue-50 svg,
        .of-shell .bg-purple-50 svg,
        .of-shell .bg-indigo-50 svg,
        .of-shell .bg-orange-50 svg,
        .of-shell .bg-teal-50 svg,
        .of-shell .bg-amber-50 svg {
            color: var(--accent) !important;
        }

        .of-shell tbody tr {
            transition: background-color .2s ease;
        }

        .of-shell tbody tr:hover {
            background-color: var(--accent-soft);
        }

        .of-shell input:focus,
        .of-shell select:focus,
        .of-shell textarea:focus {
            border-color: var(--accent) !important;
            outline-color: var(--accent);
            --tw-ring-color: var(--accent) !important;
        }

        .of-shell .of-brand-accent {
            border-top: 3px solid var(--accent) !important;
            box-shadow: 0 4px 16px color-mix(
                in srgb, var(--accent) 10%, transparent
            );
        }

        /* ========================================
           عناصر صفحة الطلبات
        ======================================== */

        .of-shell .orders-toolbar {
            background-color: var(--accent) !important;
            color: #ffffff !important;
            border-color: var(--accent) !important;
        }

        .of-shell .orders-toolbar svg,
        .of-shell .orders-header svg,
        .of-shell .orders-summary svg {
            color: inherit;
        }

        .of-shell .orders-stat {
            background-color: var(--surface) !important;
            color: var(--ink);
            border: 1px solid var(--accent) !important;
            border-top: 4px solid var(--accent) !important;
            box-shadow: 0 4px 16px color-mix(
                in srgb, var(--accent) 12%, transparent
            );
        }

        .of-shell .orders-stat-label,
        .of-shell .orders-stat-value {
            color: var(--accent) !important;
        }

        .of-shell .product-frame {
            border: 1px solid var(--accent) !important;
            box-shadow: 0 2px 8px color-mix(
                in srgb, var(--accent) 10%, transparent
            );
        }

        /* ========================================
           تثبيت خلفيات الوضع الداكن
        ======================================== */

        html[data-theme="dark"] body,
        html[data-theme="dark"] .of-shell,
        html[data-theme="dark"] .of-main,
        html[data-theme="dark"] .of-content {
            background-color: var(--page) !important;
            color: var(--ink);
        }

        html[data-theme="dark"] .of-topbar,
        html[data-theme="dark"] .of-theme-button,
        html[data-theme="dark"] .of-menu-button,
        html[data-theme="dark"] .of-shell .bg-white,
        html[data-theme="dark"] .of-shell .bg-surface {
            background-color: var(--surface) !important;
            color: var(--ink);
        }

        html[data-theme="dark"] .of-shell .bg-base,
        html[data-theme="dark"] .of-shell .bg-gray-50,
        html[data-theme="dark"] .of-shell .bg-gray-100 {
            background-color: var(--page) !important;
        }

        html[data-theme="dark"] .of-shell .bg-surface2 {
            background-color: var(--surface-hover) !important;
        }

        html[data-theme="dark"] .of-shell .orders-stat,
        html[data-theme="dark"] .of-shell .orders-table,
        html[data-theme="dark"] .of-shell .orders-table-row,
        html[data-theme="dark"] .of-shell .product-item {
            background-color: var(--surface) !important;
            color: var(--ink);
        }

        html[data-theme="dark"] .of-shell .orders-table-head,
        html[data-theme="dark"] .of-shell .product-frame {
            background-color: var(--surface-hover) !important;
        }

        html[data-theme="dark"] .of-shell .border-slate-900,
        html[data-theme="dark"] .of-shell .border-slate-800,
        html[data-theme="dark"] .of-shell .border-gray-900,
        html[data-theme="dark"] .of-shell .border-gray-800,
        html[data-theme="dark"] .of-shell .border-zinc-900,
        html[data-theme="dark"] .of-shell .border-zinc-800 {
            border-color: var(--line) !important;
        }

        html[data-theme="dark"] .of-shell input:not([type="checkbox"]):not([type="radio"]),
        html[data-theme="dark"] .of-shell select,
        html[data-theme="dark"] .of-shell textarea {
            background-color: var(--surface);
            color: var(--ink);
            border-color: var(--line);
        }

        /* ========================================
           الانتقالات والشاشات الصغيرة
        ======================================== */

        .of-shell button,
        .of-shell a,
        .of-shell [role="button"] {
            transition: background-color .2s ease, border-color .2s ease,
                        color .2s ease;
        }

        @media (max-width: 900px) {
            .of-sidebar {
                transform: translateX(100%);
                transition: transform .25s ease;
            }

            .of-sidebar.of-sidebar-open {
                transform: translateX(0);
            }

            .of-main {
                margin-right: 0;
            }

            .of-menu-button {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .of-overlay.of-overlay-visible {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .45);
                z-index: 40;
            }

            .of-topbar {
                padding: 12px 16px;
            }

            .of-content {
                padding: 18px 14px;
            }

            .of-theme-button {
                padding: 8px 10px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            #theme-toggle-label {
                display: none;
            }
        }

/* =======================================
   ألوان القائمة الجانبية في الوضع الداكن
======================================== */

html[data-theme="dark"] .of-sidebar,
html[data-theme="dark"] .of-navigation {
    background-color: #151e1a !important;
    color: #e6eee9 !important;
    border-color: #2a3831 !important;
}

html[data-theme="dark"] .of-navigation .of-nav-link {
    background-color: transparent !important;
    color: #cbd5ce !important;
    border-color: transparent !important;
}

html[data-theme="dark"] .of-navigation .of-nav-link:hover {
    background-color: #202c26 !important;
    color: #ffffff !important;
    border-color: #2a3831 !important;
}

html[data-theme="dark"] .of-navigation .of-nav-link.active {
    background-color: #26352d !important;
    color: #e6eee9 !important;
    border-color: #3b5145 !important;
    font-weight: 700;
}

html[data-theme="dark"] .of-navigation .of-nav-link.active svg {
    color: #e6eee9 !important;
}

html[data-theme="dark"] .of-navigation .of-brand-link,
html[data-theme="dark"] .of-navigation .of-account-name {
    color: #e6eee9 !important;
}

html[data-theme="dark"] .of-navigation .of-brand-caption,
html[data-theme="dark"] .of-navigation .of-account-email,
html[data-theme="dark"] .of-navigation .of-nav-heading {
    color: #9aaba1 !important;
}


    </style>
</head>

<body>
    <div class="of-shell min-h-screen">
        <div
            id="of-overlay"
            class="of-overlay"
            onclick="ofCloseSidebar()"
        ></div>

        <aside id="of-sidebar" class="of-sidebar">
            @include('layouts.navigation')
        </aside>

        <div class="of-main">
            <header class="of-topbar">
                <div style="display:flex;align-items:center;gap:12px;min-width:0">
                    <button
                        type="button"
                        class="of-menu-button"
                        onclick="ofToggleSidebar()"
                        aria-label="فتح القائمة"
                    >
                        ☰
                    </button>

                    <div style="min-width:0">
                        @isset($header)
                            {{ $header }}
                        @else
                            <div style="font-weight:700;font-size:18px">
                                OrderFlow
                            </div>
                        @endisset
                    </div>
                </div>


            </header>

            <main class="of-content">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        let ofThemeSaving = false;

        function ofUpdateThemeControls(theme) {
            const icon = document.getElementById('theme-toggle-icon');
            const label = document.getElementById('theme-toggle-label');
            const button = document.getElementById('of-theme-button');

            if (icon) {
                icon.textContent = theme === 'dark' ? '☀️' : '🌙';
            }

            if (label) {
                label.textContent =
                    theme === 'dark' ? 'الوضع الفاتح' : 'الوضع الداكن';
            }

            if (button) {
                button.setAttribute(
                    'aria-label',
                    theme === 'dark'
                        ? 'تفعيل الوضع الفاتح'
                        : 'تفعيل الوضع الداكن'
                );

                button.disabled = ofThemeSaving;
                button.style.opacity = ofThemeSaving ? '0.7' : '1';
            }
        }

        async function ofSetTheme(theme) {
            if (ofThemeSaving) return;

            const selectedTheme = theme === 'dark' ? 'dark' : 'light';

            const previousTheme =
                document.documentElement.dataset.theme === 'dark'
                    ? 'dark'
                    : 'light';

            if (selectedTheme === previousTheme) {
                ofUpdateThemeControls(selectedTheme);
                return;
            }

            ofThemeSaving = true;

            document.documentElement.dataset.theme = selectedTheme;
            ofUpdateThemeControls(selectedTheme);

            try {
                const csrfToken = document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content;

                if (!csrfToken) {
                    throw new Error('CSRF token is missing');
                }

                const response = await fetch(
                    @json(route('settings.theme')),
                    {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            theme: selectedTheme
                        })
                    }
                );

                if (!response.ok) {
                    throw new Error('Theme could not be saved');
                }

                const result = await response.json();

                if (!result.success || result.theme !== selectedTheme) {
                    throw new Error('Unexpected server response');
                }

                window.dispatchEvent(
                    new CustomEvent('orderflow-theme-changed', {
                        detail: { theme: selectedTheme }
                    })
                );
            } catch (error) {
                document.documentElement.dataset.theme = previousTheme;
                ofUpdateThemeControls(previousTheme);

                console.error('OrderFlow theme error:', error);

                alert(
                    'تعذر حفظ المظهر. تحقق من اتصالك بالإنترنت ثم حاول مجددًا.'
                );
            } finally {
                ofThemeSaving = false;

                ofUpdateThemeControls(
                    document.documentElement.dataset.theme === 'dark'
                        ? 'dark'
                        : 'light'
                );
            }
        }

        function ofToggleTheme() {
            if (ofThemeSaving) return;

            const currentTheme = document.documentElement.dataset.theme;

            ofSetTheme(currentTheme === 'dark' ? 'light' : 'dark');
        }

        function ofToggleSidebar() {
            const sidebar = document.getElementById('of-sidebar');
            const overlay = document.getElementById('of-overlay');

            if (!sidebar || !overlay) return;

            sidebar.classList.toggle('of-sidebar-open');
            overlay.classList.toggle('of-overlay-visible');
        }

        function ofCloseSidebar() {
            const sidebar = document.getElementById('of-sidebar');
            const overlay = document.getElementById('of-overlay');

            if (sidebar) sidebar.classList.remove('of-sidebar-open');
            if (overlay) overlay.classList.remove('of-overlay-visible');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const theme =
                document.documentElement.dataset.theme === 'dark'
                    ? 'dark'
                    : 'light';

            ofUpdateThemeControls(theme);
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) {
                ofCloseSidebar();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
