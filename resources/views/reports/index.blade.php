<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="chart" class="w-5 h-5 text-neon" /> التقارير
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-surface border border-line rounded p-4">
                <div class="text-sm text-muted mb-3">المبيعات والأرباح - آخر 14 يوم</div>
                <div class="relative h-80">
                  <canvas
    id="salesChart"
    data-chart='@json($chart)'
></canvas>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
