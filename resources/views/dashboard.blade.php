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


                {{-- ========================================= --}}
                {{-- أحدث الطلبات --}}
                {{-- ========================================= --}}

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

                        <div class="space-y-3">

                            @foreach($recentOrders as $order)

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

                                @endphp


                                {{-- بطاقة الطلب --}}
                                <div class="border border-line rounded-lg p-4">


                                    {{-- معلومات الطلب --}}
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


                                        {{-- الحالة --}}
                                        <div>

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

                                        </div>

                                    </div>


                                    {{-- المنتجات --}}
                                    @if($items->isNotEmpty())

                                        <div class="bg-surface2 border border-line rounded-lg p-2">

                                            <div class="space-y-1.5">

                                                @foreach($items as $item)

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

                                        {{-- الطلبات القديمة --}}
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


                                    {{-- الإجمالي --}}
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-line">

                                        <div class="text-xs text-muted">
                                            {{ number_format($totalQuantity, 0, '.', ',') }} قطعة
                                        </div>

                                        <div class="text-left">

                                            <span class="text-xs text-muted">
                                                الإجمالي
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
