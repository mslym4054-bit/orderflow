@props(['name', 'class' => 'w-4 h-4'])

@php
$paths = [
    // عام
    'plus' => 'M12 4v16m8-8H4',

    'pencil' => 'M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a2.5 2.5 0 00-3.536-3.536L4 16v4z',

    'trash' => 'M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 12a1 1 0 001 1h6a1 1 0 001-1l1-12',

    'chevron-down' => 'M6 9l6 6 6-6',

    'check-circle' => 'M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z',

    // Dashboard
    'grid' => 'M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z',

    // الطلبات
    'orders' => 'M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2zm3 4h4m-4 4h4m-4 4h2',

    // المنتجات
    'box' => 'M3 7l9-4 9 4-9 4-9-4zm0 0v10l9 4 9-4V7M12 11v10',

    // العملاء
    'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm8 0a4 4 0 11-8 0 4 4 0 018 0z',

    // التقارير
    'chart' => 'M4 19V10m5 9V5m5 14v-7m5 7V3',

    // المحادثة
    'chat' => 'M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z',

    // المجلد
    'folder' => 'M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z',

    // المستخدم
    'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',

    // الإعدادات
    'gear' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM4 12a8 8 0 1016 0 8 8 0 00-16 0z',
    'clipboard' => 'M9 5h6M9 3h6a2 2 0 012 2v1h1a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h1V5a2 2 0 012-2zm-2 6h10m-10 4h10m-10 4h6',

'package' => 'M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16zM3.27 6.96L12 12l8.73-5.04M12 22V12',

    // تسجيل الخروج
    'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m0-9H5a2 2 0 00-2 2v14a2 2 0 002 2h2',
];

$path = $paths[$name] ?? '';
@endphp

<svg
    class="{{ $class }}"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
>
    <path d="{{ $path }}" />
</svg>
