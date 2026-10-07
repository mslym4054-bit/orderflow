
<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="font-semibold text-xl text-ink">
                    لوحة التحكم
                </h2>

                <p class="text-sm text-muted mt-1">
                    نظرة سريعة على أداء متجرك اليوم
                </p>

            </div>

        </div>

    </x-slot>


    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


            {{-- ========================================= --}}
            {{-- الإحصائيات الرئيسية --}}
            {{-- ========================================= --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">


                {{-- مبيعات اليوم --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-muted">
                            مبيعات اليوم
                        </span>

                        <span class="text-xl">
                            💰
                        </span>

                    </div>

                    <div class="mt-3 text-2xl font-bold text-ink">
                        {{ number_format($todaySales, 2, '.', ',') }}
                    </div>

                    <div class="text-xs text-muted mt-1">
                        إجمالي المبيعات
                    </div>

                </div>


                {{-- أرباح اليوم --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-muted">
                            أرباح اليوم
                        </span>

                        <span class="text-xl">
                            📈
                        </span>

                    </div>

                    <div class="mt-3 text-2xl font-bold text-neon">
                        {{ number_format($todayProfit, 2, '.', ',') }}
                    </div>

                    <div class="text-xs text-muted mt-1">
                        صافي الربح
                    </div>

                </div>


                {{-- طلبات اليوم --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-muted">
                            طلبات اليوم
                        </span>

                        <span class="text-xl">
                            📦
                        </span>

                    </div>

                    <div class="mt-3 text-2xl font-bold text-ink">
                        {{ number_format($todayOrders, 0, '.', ',') }}
                    </div>

                    <div class="text-xs text-muted mt-1">
                        طلب غير ملغي
                    </div>

                </div>


                {{-- العملاء --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-muted">
                            العملاء
                        </span>

                        <span class="text-xl">
                            👥
                        </span>

                    </div>

                    <div class="mt-3 text-2xl font-bold text-ink">
                        {{ number_format($customersCount, 0, '.', ',') }}
                    </div>

                    <div class="text-xs text-muted mt-1">
                        إجمالي العملاء
                    </div>

                </div>

            </div>


            {{-- ========================================= --}}
            {{-- الرسوم البيانية --}}
            {{-- ========================================= --}}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


                {{-- المبيعات والأرباح --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="mb-5">

                        <h3 class="text-lg font-semibold text-ink">
                            المبيعات والأرباح
                        </h3>

                        <p class="text-sm text-muted mt-1">
                            أداء المتجر خلال آخر 7 أيام
                        </p>

                    </div>

                    <div class="h-80">

                        <canvas
                            id="salesProfitChart"
                            data-chart='@json($chart)'
                        ></canvas>

                    </div>

                </div>


                {{-- حالات الطلبات --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="mb-5">

                        <h3 class="text-lg font-semibold text-ink">
                            حالات الطلبات
                        </h3>

                        <p class="text-sm text-muted mt-1">
                            توزيع الطلبات حسب الحالة
                        </p>

                    </div>

                    <div class="h-80 flex items-center justify-center">

                        <canvas
                            id="orderStatusChart"
                            data-chart='@json([
                                "labels" => array_keys($statusCounts),
                                "values" => array_values($statusCounts),
                            ])'
                        ></canvas>

                    </div>

                </div>

            </div>


            {{-- ========================================= --}}
            {{-- المخزون + أحدث الطلبات --}}
            {{-- ========================================= --}}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


                {{-- تنبيهات المخزون --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="flex items-center justify-between mb-5">

                        <div>

                            <h3 class="text-lg font-semibold text-ink">
                                تنبيهات المخزون
                            </h3>

                            <p class="text-sm text-muted mt-1">
                                المنتجات التي تحتاج إلى متابعة
                            </p>

                        </div>

                        <a
                            href="{{ route('products.index') }}"
                            class="text-sm font-medium text-neon hover:underline"
                        >
                            عرض المنتجات
                        </a>

                    </div>


                    @if($lowStockProducts->count())

                        <div class="space-y-3">

                            @foreach($lowStockProducts as $product)

                                <div class="flex items-center justify-between gap-4 p-4 rounded-lg border border-line">

                                    <div class="flex items-center gap-3">

                                        <div class="w-10 h-10 rounded-lg bg-base flex items-center justify-center">
                                            📦
                                        </div>

                                        <div>

                                            <p class="font-medium text-ink">
                                                {{ $product->name }}
                                            </p>

                                            @if($product->stock == 0)

                                                <p class="text-xs text-red-500 mt-1">
                                                    نفد المخزون
                                                </p>

                                            @else

                                                <p class="text-xs text-yellow-500 mt-1">
                                                    المخزون منخفض
                                                </p>

                                            @endif

                                        </div>

                                    </div>


                                    <div class="text-left">

                                        <div class="text-lg font-bold text-ink">
                                            {{ number_format($product->stock, 0, '.', ',') }}
                                        </div>

                                        <div class="text-xs text-muted">
                                            قطعة
                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="py-10 text-center">

                            <div class="text-4xl mb-3">
                                ✅
                            </div>

                            <p class="font-medium text-ink">
                                المخزون بحالة جيدة
                            </p>

                            <p class="text-sm text-muted mt-1">
                                لا توجد منتجات منخفضة المخزون حاليًا
                            </p>

                        </div>

                    @endif

                </div>


                {{-- أحدث الطلبات --}}
                <div class="bg-surface border border-line rounded-xl p-5">

                    <div class="flex items-center justify-between mb-5">

                        <div>

                            <h3 class="text-lg font-semibold text-ink">
                                أحدث الطلبات
                            </h3>

                            <p class="text-sm text-muted mt-1">
                                آخر الطلبات المسجلة في متجرك
                            </p>

                        </div>

                        <a
                            href="{{ route('orders.index') }}"
                            class="text-sm font-medium text-neon hover:underline"
                        >
                            عرض جميع الطلبات
                        </a>

                    </div>


                    @if($recentOrders->count())

                        <div class="overflow-x-auto">

                            <table class="w-full text-sm">

                                <thead>

                                    <tr class="border-b border-line text-muted">

                                        <th class="text-right py-3 px-3 font-medium">
                                            الطلب
                                        </th>

                                        <th class="text-right py-3 px-3 font-medium">
                                            العميل
                                        </th>

                                        <th class="text-right py-3 px-3 font-medium">
                                            المنتج
                                        </th>

                                        <th class="text-right py-3 px-3 font-medium">
                                            الكمية
                                        </th>

                                        <th class="text-right py-3 px-3 font-medium">
                                            الحالة
                                        </th>

                                        <th class="text-left py-3 px-3 font-medium">
                                            الإجمالي
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach($recentOrders as $order)

                                        @php
                                            $total = ($order->price ?? 0) * ($order->quantity ?? 0);
                                        @endphp

                                        <tr class="border-b border-line last:border-0">

                                            <td class="py-4 px-3 font-medium text-ink">
                                                #{{ number_format($order->id, 0, '.', ',') }}
                                            </td>

                                            <td class="py-4 px-3 text-ink">
                                                {{ $order->customer?->name ?? '—' }}
                                            </td>

                                            <td class="py-4 px-3 text-muted">
                                                {{ $order->item }}
                                            </td>

                                            <td class="py-4 px-3 text-ink">
                                                {{ number_format($order->quantity, 0, '.', ',') }}
                                            </td>

                                            <td class="py-4 px-3">

                                                @if($order->status === 'جديد')

                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-500">
                                                        جديد
                                                    </span>

                                                @elseif($order->status === 'قيد التجهيز')

                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-500">
                                                        قيد التجهيز
                                                    </span>

                                                @elseif($order->status === 'تم الشحن')

                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500/10 text-purple-500">
                                                        تم الشحن
                                                    </span>

                                                @elseif($order->status === 'تم التسليم')

                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-500">
                                                        تم التسليم
                                                    </span>

                                                @else

                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-500">
                                                        ملغي
                                                    </span>

                                                @endif

                                            </td>

                                            <td class="py-4 px-3 text-left font-semibold text-ink">
                                                {{ number_format($total, 2, '.', ',') }}
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="py-10 text-center">

                            <div class="text-4xl mb-3">
                                📋
                            </div>

                            <p class="font-medium text-ink">
                                لا توجد طلبات حتى الآن
                            </p>

                            <p class="text-sm text-muted mt-1">
                                ستظهر أحدث الطلبات هنا عند تسجيلها
                            </p>

                        </div>

                    @endif

                </div>

            </div>


        </div>

    </div>


</x-app-layout>
