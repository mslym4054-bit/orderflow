
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="grid" class="w-5 h-5 text-neon" />
            {{ app()->getLocale() === 'en' ? 'Orders' : 'الطلبات' }}
        </h2>
    </x-slot>

    @php
        $locale = auth()->user()?->locale ?? app()->getLocale();
        $isEnglish = $locale === 'en';

        $statusLabels = [
            'جديد' => $isEnglish ? 'New' : 'جديد',
            'قيد التجهيز' => $isEnglish ? 'Processing' : 'قيد التجهيز',
            'تم الشحن' => $isEnglish ? 'Shipped' : 'تم الشحن',
            'تم التسليم' => $isEnglish ? 'Delivered' : 'تم التسليم',
            'ملغي' => $isEnglish ? 'Cancelled' : 'ملغي',
        ];
    @endphp

    <div class="py-6 orders-page">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 bg-neon/10 text-neon rounded border border-neon/20 flex items-center gap-2">
                    <x-icon name="check-circle" />
                    {{ session('status') }}
                </div>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                <div class="orders-stat bg-surface rounded p-4">
                    <div class="orders-stat-label text-sm">
                        {{ $isEnglish ? 'Sales This Month' : 'مبيعات هذا الشهر' }}
                    </div>
                    <div class="orders-stat-value text-2xl font-bold mt-1">
                        {{ number_format($stats['revenue'], 2, '.', ',') }}
                    </div>
                </div>

                <div class="orders-stat bg-surface rounded p-4">
                    <div class="orders-stat-label text-sm">
                        {{ $isEnglish ? 'Costs' : 'التكاليف' }}
                    </div>
                    <div class="orders-stat-value text-2xl font-bold mt-1">
                        {{ number_format($stats['cost'], 2, '.', ',') }}
                    </div>
                </div>

                <div class="orders-stat bg-surface rounded p-4">
                    <div class="orders-stat-label text-sm">
                        {{ $isEnglish ? 'Net Profit' : 'صافي الربح' }}
                    </div>
                    <div class="text-2xl font-bold mt-1 {{ $stats['profit'] >= 0 ? 'text-neon' : 'text-red-400' }}">
                        {{ number_format($stats['profit'], 2, '.', ',') }}
                    </div>
                </div>
            </div>

            {{-- Filters and create button --}}
            <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('orders.index') }}"
                       class="px-3 py-1 rounded text-sm {{ !$currentStatus ? 'orders-filter-active font-medium' : 'bg-surface2 border border-line text-muted hover:text-ink' }}">
                        {{ $isEnglish ? 'All' : 'الكل' }}
                    </a>

                    @foreach ($statuses as $status)
                        <a href="{{ route('orders.index', ['status' => $status]) }}"
                           class="px-3 py-1 rounded text-sm {{ $currentStatus === $status ? 'orders-filter-active font-medium' : 'bg-surface2 border border-line text-muted hover:text-ink' }}">
                            {{ $statusLabels[$status] ?? $status }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('orders.create') }}"
                   class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold flex items-center gap-2">
                    <x-icon name="plus" />
                    {{ $isEnglish ? 'New Order' : 'طلب جديد' }}
                </a>
            </div>

            {{-- Orders table --}}
            <div class="bg-surface border border-line rounded overflow-x-auto orders-table">
                <table class="w-full text-sm text-right">
                    <thead class="bg-surface2 orders-table-head">
                        <tr>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Customer' : 'العميل' }}</th>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Products' : 'المنتجات' }}</th>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Quantity' : 'الكمية' }}</th>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Total Cost' : 'إجمالي التكلفة' }}</th>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Total Sales' : 'إجمالي البيع' }}</th>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Net Profit' : 'صافي الربح' }}</th>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Status' : 'الحالة' }}</th>
                            <th class="p-3 text-muted font-medium">{{ $isEnglish ? 'Date' : 'تاريخ' }}</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $badge = match ($order->status) {
                                    'جديد' => 'bg-white/5 text-muted',
                                    'قيد التجهيز' => 'bg-amber-400/10 text-amber-300',
                                    'تم الشحن' => 'bg-neon/10 text-neon',
                                    'تم التسليم' => 'bg-emerald-400/10 text-emerald-300',
                                    'ملغي' => 'bg-red-400/10 text-red-300',
                                    default => 'bg-white/5 text-muted',
                                };
                            @endphp

                            <tr class="border-t border-line orders-table-row">
                                {{-- Customer --}}
                                <td class="p-3 text-ink align-top">
                                    {{ $order->customer?->name ?? ($isEnglish ? 'No customer' : 'بدون عميل') }}

                                    @if ($order->customer?->phone)
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $order->customer->phone) }}"
                                           target="_blank"
                                           rel="noopener"
                                           class="text-xs text-neon hover:text-neon-dim flex items-center gap-1 mt-1">
                                            <x-icon name="chat" class="w-3 h-3" />
                                            {{ $order->customer->phone }}
                                        </a>
                                    @endif
                                </td>

                                {{-- Products --}}
                                <td class="p-3 align-top">
                                    @if ($order->items->isNotEmpty())
                                        <div class="product-frame bg-surface2 rounded-lg p-2 min-w-[240px]">
                                            <div class="space-y-1.5">
                                                @foreach ($order->items as $item)
                                                    <div class="product-item flex items-center justify-between gap-3 px-3 py-2 rounded-md bg-surface">
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            <div class="product-icon w-7 h-7 rounded-md flex items-center justify-center shrink-0">
                                                                <x-icon name="package" class="w-4 h-4" />
                                                            </div>
                                                            <span class="text-ink font-medium truncate">
                                                                {{ $item->item }}
                                                            </span>
                                                        </div>

                                                        <div class="flex items-center gap-2 shrink-0 text-xs">
                                                            <span class="product-quantity px-2 py-1 rounded">
                                                                {{ $isEnglish ? 'Qty:' : 'العدد:' }}
                                                                {{ number_format($item->quantity, 0, '.', ',') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="product-frame bg-surface2 rounded-lg px-3 py-2 min-w-[240px]">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-ink font-medium">
                                                    {{ $order->item }}
                                                </span>
                                                <span class="product-quantity px-2 py-1 rounded text-xs">
                                                    {{ $isEnglish ? 'Qty:' : 'العدد:' }}
                                                    {{ number_format($order->quantity, 0, '.', ',') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                {{-- Total quantity --}}
                                <td class="p-3 text-ink align-top">
                                    {{ number_format($order->display_quantity, 0, '.', ',') }}
                                </td>

                                {{-- Total cost --}}
                                <td class="p-3 text-ink align-top">
                                    {{ number_format($order->display_cost, 2, '.', ',') }}
                                </td>

                                {{-- Total sales --}}
                                <td class="p-3 text-ink align-top">
                                    {{ number_format($order->display_revenue, 2, '.', ',') }}
                                </td>

                                {{-- Net profit --}}
                                <td class="p-3 align-top {{ $order->display_profit >= 0 ? 'text-neon' : 'text-red-400' }}">
                                    {{ number_format($order->display_profit, 2, '.', ',') }}
                                </td>

                                {{-- Status --}}
                                <td class="p-3 align-top">
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $badge }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td class="p-3 text-muted align-top">
                                    {{ $order->created_at->format('Y-m-d') }}
                                </td>

                                {{-- Actions --}}
                                <td class="p-3 align-top whitespace-nowrap">
                                    <a href="{{ route('orders.edit', $order) }}"
                                       class="text-neon hover:text-neon-dim font-medium inline-flex items-center gap-1">
                                        <x-icon name="pencil" />
                                        {{ $isEnglish ? 'Edit' : 'تعديل' }}
                                    </a>

                                    <a href="{{ route('orders.invoice', $order) }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="text-muted hover:text-ink font-medium inline-flex items-center gap-1 mr-2">
                                        <x-icon name="folder" class="w-4 h-4" />
                                        {{ $isEnglish ? 'Invoice' : 'فاتورة' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-muted">
                                    <x-icon name="folder" class="w-8 h-8 mx-auto mb-2" />
                                    {{ $isEnglish ? 'No orders yet.' : 'لا توجد طلبات حتى الآن.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    {{-- Theme-aware styles --}}
    <style>
        .orders-page .orders-stat {
            background-color: var(--surface) !important;
            border: 1px solid var(--accent) !important;
            border-top: 4px solid var(--accent) !important;
            box-shadow: 0 4px 16px color-mix(in srgb, var(--accent) 10%, transparent);
        }

        .orders-page .orders-stat-label,
        .orders-page .orders-stat-value {
            color: var(--accent) !important;
        }

        .orders-page .orders-stat .text-red-400 {
            color: #f87171 !important;
        }

        .orders-page .orders-filter-active {
            background-color: var(--accent) !important;
            color: var(--accent-contrast, #ffffff) !important;
            border: 1px solid var(--accent) !important;
        }

        .orders-page .orders-table {
            background-color: var(--surface) !important;
        }

        .orders-page .orders-table-head {
            background-color: var(--surface-hover) !important;
        }

        .orders-page .orders-table-head th {
            border-bottom: 2px solid var(--accent) !important;
        }

        .orders-page .orders-table-row {
            background-color: var(--surface) !important;
        }

        .orders-page .product-frame {
            background-color: var(--surface-hover) !important;
            border: 1px solid var(--accent) !important;
        }

        .orders-page .product-item {
            background-color: var(--surface) !important;
            border: 1px solid var(--line) !important;
        }

        .orders-page .product-icon,
        .orders-page .product-quantity {
            background-color: var(--accent-soft) !important;
            color: var(--accent) !important;
        }

        .orders-page .bg-neon {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
            color: var(--accent-contrast, #ffffff) !important;
        }

        .orders-page .hover\:bg-neon-dim:hover {
            background-color: var(--accent-hover) !important;
            border-color: var(--accent-hover) !important;
            color: #ffffff !important;
        }

        .orders-page .text-neon {
            color: var(--accent) !important;
        }

        .orders-page .bg-neon\/10 {
            background-color: var(--accent-soft) !important;
        }

        .orders-page .border-neon\/20 {
            border-color: var(--accent) !important;
        }

        /* Dark mode */
        html[data-theme="dark"] .orders-page .orders-stat,
        html[data-theme="dark"] .orders-page .orders-table,
        html[data-theme="dark"] .orders-page .orders-table-row,
        html[data-theme="dark"] .orders-page .product-item {
            background-color: var(--surface) !important;
        }

        html[data-theme="dark"] .orders-page .orders-table-head,
        html[data-theme="dark"] .orders-page .product-frame {
            background-color: var(--surface-hover) !important;
        }

        html[data-theme="dark"] .orders-page .orders-stat-label,
        html[data-theme="dark"] .orders-page .orders-stat-value,
        html[data-theme="dark"] .orders-page .product-icon,
        html[data-theme="dark"] .orders-page .product-quantity {
            color: var(--accent) !important;
        }
    </style>
</x-app-layout>
