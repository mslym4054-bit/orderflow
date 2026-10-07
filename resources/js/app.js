import Chart from 'chart.js/auto';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {

    // Sales & Profit Chart - Dashboard
    const canvas = document.getElementById('salesProfitChart');

    if (canvas) {
        const chartData = JSON.parse(canvas.dataset.chart);

        new Chart(canvas, {
            type: 'line',

            data: {
                labels: chartData.labels,

                datasets: [
                    {
                        label: 'Sales',
                        data: chartData.sales,
                        tension: 0.4,
                        fill: false,
                    },
                    {
                        label: 'Profit',
                        data: chartData.profit,
                        tension: 0.4,
                        fill: false,
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        position: 'top',
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            callback: function(value) {
                                return Number(value).toLocaleString('en-US');
                            }
                        }
                    }
                }
            }
        });
    }


    // Order Status Chart - Dashboard
    const statusCanvas = document.getElementById('orderStatusChart');

    if (statusCanvas) {
        const statusData = JSON.parse(statusCanvas.dataset.chart);

        new Chart(statusCanvas, {
            type: 'doughnut',

            data: {
                labels: statusData.labels,

                datasets: [
                    {
                        data: statusData.values,
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }


    // Reports Sales & Profit Chart
    const reportCanvas = document.getElementById('salesChart');

    if (reportCanvas) {
        const reportData = JSON.parse(reportCanvas.dataset.chart);

        Chart.defaults.font.family = 'Tajawal, sans-serif';
        Chart.defaults.color = '#8B98A0';

        new Chart(reportCanvas, {
            type: 'line',

            data: {
                labels: reportData.labels,

                datasets: [
                    {
                        label: 'المبيعات',
                        data: reportData.revenue,
                        borderColor: '#E7EDEC',
                        backgroundColor: 'rgba(231,237,236,0.08)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'الربح',
                        data: reportData.profit,
                        borderColor: '#39FFC2',
                        backgroundColor: 'rgba(57,255,194,0.12)',
                        tension: 0.3,
                        fill: true,
                    },
                ],
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    x: {
                        grid: {
                            color: '#262E35'
                        }
                    },

                    y: {
                        grid: {
                            color: '#262E35'
                        },

                        beginAtZero: true,

                        ticks: {
                            callback: function(value) {
                                return Number(value).toLocaleString('en-US');
                            }
                        }
                    },
                },
            },
        });
    }

});
