
<x-app-layout>
    @php
        $locale = auth()->user()?->locale ?? app()->getLocale();
        $isEnglish = $locale === 'en';

        $t = function ($ar, $en) use ($isEnglish) {
            return $isEnglish ? $en : $ar;
        };

        $money = function ($value) {
            return number_format((float) $value, 2, '.', ',');
        };

        $translatedStatuses = [
            'جديد' => 'New',
            'قيد التجهيز' => 'Processing',
            'تم الشحن' => 'Shipped',
            'تم التسليم' => 'Delivered',
            'ملغي' => 'Cancelled',
        ];

        $reportStatusLabels = [];

        foreach (array_keys($statusCounts ?? []) as $status) {
            $reportStatusLabels[] = $isEnglish
                ? ($translatedStatuses[$status] ?? $status)
                : $status;
        }

        $reportStatusData = [
            'labels' => $reportStatusLabels,
            'values' => array_values($statusCounts ?? []),
        ];

        $topProducts = collect($topProducts ?? [])->take(5)->values();

        $topProductsChartData = [
            'labels' => $topProducts->pluck('name')->values()->all(),
            'values' => $topProducts->pluck('quantity')->values()->all(),
        ];

        $monthlyRecords = collect($monthlyRecords ?? [])->take(12)->values();

        $stats = array_merge([
            'revenue' => 0,
            'cost' => 0,
            'profit' => 0,
            'orders' => 0,
            'quantity' => 0,
        ], $stats ?? []);

        $chart = $chart ?? [
            'labels' => [],
            'revenue' => [],
            'profit' => [],
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-ink">
                    {{ $t('التقارير والتحليلات', 'Reports & Analytics') }}
                </h2>
                <p class="text-sm text-muted mt-1">
                    {{ $t('نظرة شاملة على أداء متجرك ومبيعاتك', 'An overview of your store performance and sales') }}
                </p>
            </div>

            <span class="text-sm text-muted">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div class="rounded-2xl border border-line bg-surface p-5">
                    <p class="text-sm text-muted">
                        {{ $t('مبيعات هذا الشهر', 'Sales This Month') }}
                    </p>
                    <p class="mt-3 text-2xl font-bold text-ink">
                        {{ $money($stats['revenue']) }}
                    </p>
                    <p class="mt-2 text-xs text-muted">
                        {{ $t('قيمة الطلبات غير الملغاة', 'Value of non-cancelled orders') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-line bg-surface p-5">
                    <p class="text-sm text-muted">
                        {{ $t('صافي الربح هذا الشهر', 'Net Profit This Month') }}
                    </p>
                    <p class="mt-3 text-2xl font-bold text-emerald-400">
                        {{ $money($stats['profit']) }}
                    </p>
                    <p class="mt-2 text-xs text-muted">
                        {{ $t('المبيعات ناقص تكلفة البضاعة', 'Sales minus product costs') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-line bg-surface p-5">
                    <p class="text-sm text-muted">
                        {{ $t('تكلفة البضاعة', 'Cost of Goods') }}
                    </p>
                    <p class="mt-3 text-2xl font-bold text-ink">
                        {{ $money($stats['cost']) }}
                    </p>
                    <p class="mt-2 text-xs text-muted">
                        {{ $t('تكلفة المنتجات المباعة هذا الشهر', 'Cost of products sold this month') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-line bg-surface p-5">
                    <p class="text-sm text-muted">
                        {{ $t('عدد الطلبات', 'Total Orders') }}
                    </p>
                    <p class="mt-3 text-2xl font-bold text-ink">
                        {{ number_format((int) $stats['orders'], 0, '.', ',') }}
                    </p>
                    <p class="mt-2 text-xs text-muted">
                        {{ number_format((int) $stats['quantity'], 0, '.', ',') }}
                        {{ $t('قطعة مباعة', 'items sold') }}
                    </p>
                </div>

            </div>

            {{-- Monthly sales and profit --}}
            <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-ink">
                        {{ $t('المبيعات والأرباح الشهرية', 'Monthly Sales & Profit') }}
                    </h3>
                    <p class="text-sm text-muted mt-1">
                        {{ $t('مقارنة المبيعات والأرباح خلال آخر 12 شهرًا', 'Sales and profit over the last 12 months') }}
                    </p>
                </div>

                <div class="relative h-80">
                    <canvas
                        id="salesChart"
                        data-chart="{{ json_encode($chart, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) }}"
                    ></canvas>
                </div>
            </section>

            {{-- Status and top products charts --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                    <h3 class="text-lg font-semibold text-ink">
                        {{ $t('حالات الطلبات', 'Order Statuses') }}
                    </h3>
                    <p class="text-sm text-muted mt-1 mb-5">
                        {{ $t('توزيع الطلبات حسب الحالة', 'Distribution of orders by status') }}
                    </p>

                    <div class="relative h-80 flex items-center justify-center">
                        <canvas
                            id="orderStatusReportChart"
                            data-chart="{{ json_encode($reportStatusData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) }}"
                        ></canvas>
                    </div>
                </section>

                <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                    <h3 class="text-lg font-semibold text-ink">
                        {{ $t('المنتجات الأكثر مبيعًا', 'Top-Selling Products') }}
                    </h3>
                    <p class="text-sm text-muted mt-1 mb-5">
                        {{ $t('أعلى 5 منتجات حسب الكمية المباعة', 'Top 5 products by quantity sold') }}
                    </p>

                    <div class="relative h-80">
                        <canvas
                            id="topProductsChart"
                            data-chart="{{ json_encode($topProductsChartData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) }}"
                        ></canvas>
                    </div>
                </section>

            </div>

            {{-- Monthly history: 12 months --}}
            <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-ink">
                        {{ $t('السجل الشهري', 'Monthly History') }}
                    </h3>
                    <p class="text-sm text-muted mt-1">
                        {{ $t('تفاصيل المبيعات والتكاليف والأرباح لآخر 12 شهرًا', 'Sales, costs, and profit for the last 12 months') }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-line">
                        <thead>
                            <tr class="text-sm text-muted">
                                <th class="px-4 py-3 text-start font-medium">{{ $t('الشهر', 'Month') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $t('المبيعات', 'Sales') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $t('التكلفة', 'Cost') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $t('صافي الربح', 'Net Profit') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $t('الطلبات', 'Orders') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $t('القطع المباعة', 'Items Sold') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-line">
                            @forelse ($monthlyRecords as $record)
                                <tr class="text-sm">
                                    <td class="px-4 py-4 font-medium text-ink whitespace-nowrap">
                                        {{ $record['month'] }}
                                    </td>
                                    <td class="px-4 py-4 text-ink whitespace-nowrap">
                                        {{ $money($record['revenue']) }}
                                    </td>
                                    <td class="px-4 py-4 text-muted whitespace-nowrap">
                                        {{ $money($record['cost']) }}
                                    </td>
                                    <td class="px-4 py-4 font-semibold whitespace-nowrap {{ $record['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                        {{ $money($record['profit']) }}
                                    </td>
                                    <td class="px-4 py-4 text-ink">
                                        {{ number_format((int) $record['orders'], 0, '.', ',') }}
                                    </td>
                                    <td class="px-4 py-4 text-ink">
                                        {{ number_format((int) $record['quantity'], 0, '.', ',') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-muted">
                                        {{ $t('لا توجد بيانات لعرضها حاليًا', 'No data available yet') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Top products table --}}
            <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-ink">
                        {{ $t('تفاصيل المنتجات الأكثر مبيعًا', 'Top Products Details') }}
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-line">
                        <thead>
                            <tr class="text-sm text-muted">
                                <th class="px-4 py-3 text-start font-medium">{{ $t('المنتج', 'Product') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $t('الكمية المباعة', 'Quantity Sold') }}</th>
                                <th class="px-4 py-3 text-start font-medium">{{ $t('إجمالي المبيعات', 'Total Sales') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-line">
                            @forelse ($topProducts as $product)
                                <tr class="text-sm">
                                    <td class="px-4 py-4 font-medium text-ink">
                                        {{ $product['name'] }}
                                    </td>
                                    <td class="px-4 py-4 text-ink">
                                        {{ number_format((int) $product['quantity'], 0, '.', ',') }}
                                    </td>
                                    <td class="px-4 py-4 text-ink whitespace-nowrap">
                                        {{ $money($product['revenue']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center text-muted">
                                        {{ $t('لا توجد مبيعات مسجلة حتى الآن', 'No sales recorded yet') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
