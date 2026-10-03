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
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const data = @json($chart);
        Chart.defaults.font.family = 'Tajawal, sans-serif';
        Chart.defaults.color = '#8B98A0';
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'المبيعات', data: data.revenue, borderColor: '#E7EDEC', backgroundColor: 'rgba(231,237,236,0.08)', tension: 0.3, fill: true },
                    { label: 'الربح', data: data.profit, borderColor: '#39FFC2', backgroundColor: 'rgba(57,255,194,0.12)', tension: 0.3, fill: true },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { color: '#262E35' } },
                    y: { grid: { color: '#262E35' }, beginAtZero: true },
                },
            },
        });
    });
    </script>
</x-app-layout>
