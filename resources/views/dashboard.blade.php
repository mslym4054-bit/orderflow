
<x-app-layout>
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

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-ink">
                    {{ $isEnglish ? 'Dashboard' : 'لوحة التحكم' }}
                </h2>
                <p class="text-sm text-muted mt-1">
                    {{ $isEnglish ? 'A quick overview of your store performance today' : 'نظرة سريعة على أداء متجرك اليوم' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Main statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">
                            {{ $isEnglish ? "Today's Sales" : 'مبيعات اليوم' }}
                        </span>
                        <span class="text-xl">💰</span>
                    </div>
                    <div class="mt-3 text-2xl font-bold text-ink">
                        {{ number_format($todaySales, 2, '.', ',') }}
                    </div>
                    <div class="text-xs text-muted mt-1">
                        {{ $isEnglish ? 'Total Sales' : 'إجمالي المبيعات' }}
                    </div>
                </div>

                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">
                            {{ $isEnglish ? "Today's Profit" : 'أرباح اليوم' }}
                        </span>
                        <span class="text-xl">📈</span>
                    </div>
                    <div class="mt-3 text-2xl font-bold text-neon">
                        {{ number_format($todayProfit, 2, '.', ',') }}
                    </div>
                    <div class="text-xs text-muted mt-1">
                        {{ $isEnglish ? 'Net Profit' : 'صافي الربح' }}
                    </div>
                </div>

                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">
                            {{ $isEnglish ? "Today's Orders" : 'طلبات اليوم' }}
                        </span>
                        <span class="text-xl">📦</span>
                    </div>
                    <div class="mt-3 text-2xl font-bold text-ink">
                        {{ number_format($todayOrders, 0, '.', ',') }}
                    </div>
                    <div class="text-xs text-muted mt-1">
                        {{ $isEnglish ? 'Non-cancelled orders' : 'طلب غير ملغي' }}
                    </div>
                </div>

                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">
                            {{ $isEnglish ? 'Customers' : 'العملاء' }}
                        </span>
                        <span class="text-xl">👥</span>
                    </div>
                    <div class="mt-3 text-2xl font-bold text-ink">
                        {{ number_format($customersCount, 0, '.', ',') }}
                    </div>
                    <div class="text-xs text-muted mt-1">
                        {{ $isEnglish ? 'Total Customers' : 'إجمالي العملاء' }}
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="mb-5">
                        <h3 class="text-lg font-semibold text-ink">
                            {{ $isEnglish ? 'Sales & Profit' : 'المبيعات والأرباح' }}
                        </h3>
                        <p class="text-sm text-muted mt-1">
                            {{ $isEnglish ? 'Store performance over the last 7 days' : 'أداء المتجر خلال آخر 7 أيام' }}
                        </p>
                    </div>
                    <div class="h-80">
                        <canvas id="salesProfitChart" data-chart='@json($chart)'></canvas>
                    </div>
                </div>

                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="mb-5">
                        <h3 class="text-lg font-semibold text-ink">
                            {{ $isEnglish ? 'Order Status' : 'حالات الطلبات' }}
                        </h3>
                        <p class="text-sm text-muted mt-1">
                            {{ $isEnglish ? 'Orders by Status' : 'توزيع الطلبات حسب الحالة' }}
                        </p>
                    </div>
                    <div class="h-80 flex items-center justify-center">
                        @php
                            $chartLabels = array_map(
                                fn ($status) => $statusLabels[$status] ?? $status,
                                array_keys($statusCounts)
                            );
                        @endphp

                        <canvas
                            id="orderStatusChart"
                            data-chart='@json([
                                "labels" => $chartLabels,
                                "values" => array_values($statusCounts),
                            ])'
                        ></canvas>
                    </div>
                </div>
            </div>

            {{-- Stock alerts and recent orders --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Stock alerts --}}
                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-ink">
                                {{ $isEnglish ? 'Stock Alerts' : 'تنبيهات المخزون' }}
                            </h3>
                            <p class="text-sm text-muted mt-1">
                                {{ $isEnglish ? 'Products that need attention' : 'المنتجات التي تحتاج إلى متابعة' }}
                            </p>
                        </div>

                        <a href="{{ route('products.index') }}"
                           class="text-sm font-medium text-neon hover:underline">
                            {{ $isEnglish ? 'View Products' : 'عرض المنتجات' }}
                        </a>
                    </div>

                    @if ($lowStockProducts->count())
                        <div class="space-y-3">
                            @foreach ($lowStockProducts as $product)
                                <div class="flex items-center justify-between gap-4 p-4 rounded-lg border border-line">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-base flex items-center justify-center">
                                            📦
                                        </div>
                                        <div>
                                            <p class="font-medium text-ink">
                                                {{ $product->name }}
                                            </p>

                                            @if ($product->stock == 0)
                                                <p class="text-xs text-red-500 mt-1">
                                                    {{ $isEnglish ? 'Out of stock' : 'نفد المخزون' }}
                                                </p>
                                            @else
                                                <p class="text-xs text-yellow-500 mt-1">
                                                    {{ $isEnglish ? 'Low stock' : 'المخزون منخفض' }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-left">
                                        <div class="text-lg font-bold text-ink">
                                            {{ number_format($product->stock, 0, '.', ',') }}
                                        </div>
                                        <div class="text-xs text-muted">
                                            {{ $isEnglish ? 'items' : 'قطعة' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center">
                            <div class="text-4xl mb-3">✅</div>
                            <p class="font-medium text-ink">
                                {{ $isEnglish ? 'Stock is in good shape' : 'المخزون بحالة جيدة' }}
                            </p>
                            <p class="text-sm text-muted mt-1">
                                {{ $isEnglish ? 'No low-stock products at the moment' : 'لا توجد منتجات منخفضة المخزون حاليًا' }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Recent orders --}}
                <div class="bg-surface border border-line rounded-xl p-5">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-ink">
                                {{ $isEnglish ? 'Recent Orders' : 'أحدث الطلبات' }}
                            </h3>
                            <p class="text-sm text-muted mt-1">
                                {{ $isEnglish ? 'Your latest store orders' : 'أحدث طلبات متجرك' }}
                            </p>
                        </div>

                        <a href="{{ route('orders.index') }}"
                           class="text-sm font-medium text-neon hover:underline">
                            {{ $isEnglish ? 'View All Orders' : 'عرض جميع الطلبات' }}
                        </a>
                    </div>

                    @if ($recentOrders->count())
                        <div class="space-y-3">
                            @foreach ($recentOrders as $order)
                                @php
                                    $items = $order->items;

                                    if ($items->isNotEmpty()) {
                                        $total = $items->sum(function ($item) {
                                            return ($item->price ?? 0) * $item->quantity;
                                        });

                                        $totalQuantity = $items->sum('quantity');
                                    } else {
                                        $total = ($order->price ?? 0) * ($order->quantity ?? 0);
                                        $totalQuantity = $order->quantity ?? 0;
                                    }

                                    $badgeClasses = match ($order->status) {
                                        'جديد' => 'bg-blue-500/10 text-blue-500',
                                        'قيد التجهيز' => 'bg-yellow-500/10 text-yellow-500',
                                        'تم الشحن' => 'bg-purple-500/10 text-purple-500',
                                        'تم التسليم' => 'bg-green-500/10 text-green-500',
                                        default => 'bg-red-500/10 text-red-500',
                                    };
                                @endphp

                                <div class="border border-line rounded-lg p-4">
                                    {{-- Order information --}}
                                    <div class="flex items-center justify-between gap-4 mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-base flex items-center justify-center shrink-0">
                                                📦
                                            </div>
                                            <div>
                                                <div class="font-semibold text-ink">
                                                    #{{ number_format($order->id, 0, '.', ',') }}
                                                </div>
                                                <div class="text-xs text-muted mt-1">
                                                    {{ $order->customer?->name ?? '—' }}
                                                </div>
                                            </div>
                                        </div>

                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </div>

                                    {{-- Products --}}
                                    @if ($items->isNotEmpty())
                                        <div class="bg-surface2 border border-line rounded-lg p-2">
                                            <div class="space-y-1.5">
                                                @foreach ($items as $item)
                                                    <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-md bg-surface border border-line">
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            <div class="w-7 h-7 rounded-md bg-neon/10 text-neon flex items-center justify-center shrink-0">
                                                                <x-icon name="package" class="w-4 h-4" />
                                                            </div>
                                                            <span class="text-ink font-medium truncate">
                                                                {{ $item->item }}
                                                            </span>
                                                        </div>

                                                        <span class="px-2 py-1 rounded bg-white/5 text-muted text-xs shrink-0">
                                                            × {{ number_format($item->quantity, 0, '.', ',') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-surface2 border border-line rounded-lg px-3 py-2">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-ink font-medium truncate">
                                                    {{ $order->item }}
                                                </span>
                                                <span class="px-2 py-1 rounded bg-white/5 text-muted text-xs shrink-0">
                                                    × {{ number_format($order->quantity, 0, '.', ',') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Total --}}
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-line">
                                        <div class="text-xs text-muted">
                                            {{ number_format($totalQuantity, 0, '.', ',') }}
                                            {{ $isEnglish ? 'items' : 'قطعة' }}
                                        </div>

                                        <div class="text-left">
                                            <span class="text-xs text-muted">
                                                {{ $isEnglish ? 'Total' : 'الإجمالي' }}
                                            </span>
                                            <span class="font-bold text-ink mr-2">
                                                {{ number_format($total, 2, '.', ',') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center">
                            <div class="text-4xl mb-3">📋</div>
                            <p class="font-medium text-ink">
                                {{ $isEnglish ? 'No orders yet' : 'لا توجد طلبات حتى الآن' }}
                            </p>
                            <p class="text-sm text-muted mt-1">
                                {{ $isEnglish ? 'Your recent orders will appear here once you add them' : 'ستظهر أحدث الطلبات هنا عند تسجيلها' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
