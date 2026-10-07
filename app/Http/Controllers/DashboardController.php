<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // مبيعات اليوم
        $todaySales = $user->orders()
            ->where('status', '!=', 'ملغي')
            ->whereDate('created_at', today())
            ->sum(DB::raw('price * quantity'));

        // أرباح اليوم
        $todayProfit = $user->orders()
            ->where('status', '!=', 'ملغي')
            ->whereDate('created_at', today())
            ->sum(DB::raw('(price - cost) * quantity'));

        // عدد طلبات اليوم
        $todayOrders = $user->orders()
            ->where('status', '!=', 'ملغي')
            ->whereDate('created_at', today())
            ->count();

        // عدد العملاء
        $customersCount = $user->customers()->count();
        // مبيعات وأرباح آخر 7 أيام
$daily = $user->orders()
    ->where('status', '!=', 'ملغي')
    ->where('created_at', '>=', now()->subDays(6)->startOfDay())
    ->selectRaw('DATE(created_at) as day')
    ->selectRaw('COALESCE(SUM(price * quantity), 0) as sales')
    ->selectRaw('COALESCE(SUM((price - cost) * quantity), 0) as profit')
    ->groupBy('day')
    ->get()
    ->keyBy('day');

$chart = [
    'labels' => [],
    'sales' => [],
    'profit' => [],
];

for ($i = 6; $i >= 0; $i--) {
    $date = now()->subDays($i);

    $row = $daily->get($date->format('Y-m-d'));

    $chart['labels'][] = $date->format('m/d');
    $chart['sales'][] = $row ? (float) $row->sales : 0;
    $chart['profit'][] = $row ? (float) $row->profit : 0;
}
        // المنتجات منخفضة المخزون
        $lowStockProducts = $user->products()
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        // أحدث 5 طلبات
        $recentOrders = $user->orders()
            ->with(['customer', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        // عدد الطلبات حسب الحالة
        $statusCounts = [];

        foreach (Order::STATUSES as $status) {
            $statusCounts[$status] = $user->orders()
                ->where('status', $status)
                ->count();
        }

       return view('dashboard', compact(
            'todaySales',
            'todayProfit',
            'todayOrders',
            'customersCount',
            'lowStockProducts',
            'recentOrders',
            'statusCounts',
            'chart'
        ));
    }
}
