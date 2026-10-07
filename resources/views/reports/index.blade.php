<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="chart" class="w-5 h-5 text-neon" />
            التقارير
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Sales & Profit Report --}}
            <div class="bg-surface border border-line rounded-xl p-5">

                <div class="mb-5">

                    <h3 class="text-lg font-semibold text-ink">
                        المبيعات والأرباح
                    </h3>

                    <p class="text-sm text-muted mt-1">
                        أداء المتجر خلال آخر 14 يوم
                    </p>

                </div>

                <div class="relative h-80">

                    <canvas
                        id="salesChart"
                        data-chart='@json($chart)'
                    ></canvas>

                </div>

            </div>


            {{-- Order Status Report --}}
            <div class="bg-surface border border-line rounded-xl p-5">

                <div class="mb-5">

                    <h3 class="text-lg font-semibold text-ink">
                        حالات الطلبات
                    </h3>

                    <p class="text-sm text-muted mt-1">
                        توزيع الطلبات حسب الحالة
                    </p>

                </div>

                <div class="relative h-80 flex items-center justify-center">

                    <canvas
                        id="orderStatusReportChart"
                        data-chart='@json([
                            "labels" => array_keys($statusCounts),
                            "values" => array_values($statusCounts),
                        ])'
                    ></canvas>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
