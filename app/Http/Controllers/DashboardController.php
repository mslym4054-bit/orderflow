<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        /*
         * طلبات اليوم غير الملغاة
         */
        $todayOrdersCollection = $user->orders()
            ->where('status', '!=', 'ملغي')
            ->whereDate('created_at', today())
            ->with('items')
            ->get();

        /*
         * مبيعات اليوم + أرباح اليوم
         *
         * الطلبات الجديدة:
         * نحسب من order_items.
         *
         * الطلبات القديمة:
         * نحسب من الحقول القديمة داخل orders.
         */
        $todaySales = 0;
        $todayCost = 0;

        foreach ($todayOrdersCollection as $order) {

            if ($order->items->isNotEmpty()) {

                foreach ($order->items as $item) {

                    $todaySales +=
                        ($item->price ?? 0) * $item->quantity;

                    $todayCost +=
                        ($item->cost ?? 0) * $item->quantity;
                }

                continue;
            }

            /*
             * الطلبات القديمة
             */
            $todaySales +=
                ($order->price ?? 0) * ($order->quantity ?? 0);

            $todayCost +=
                ($order->cost ?? 0) * ($order->quantity ?? 0);
        }

        $todayProfit = $todaySales - $todayCost;

        /*
         * عدد طلبات اليوم
         */
        $todayOrders = $todayOrdersCollection->count();

        /*
         * عدد العملاء
         */
        $customersCount = $user->customers()->count();

        /*
         * مبيعات وأرباح آخر 7 أيام
         *
         * نستخدم order_items للطلبات الجديدة،
         * مع الحفاظ على دعم الطلبات القديمة.
         */
        $dailyOrders = $user->orders()
            ->where('status', '!=', 'ملغي')
            ->where(
                'created_at',
                '>=',
                now()->subDays(6)->startOfDay()
            )
            ->with('items')
            ->get();

        $daily = [];

        foreach ($dailyOrders as $order) {

            $day = $order->created_at->format('Y-m-d');

            if (! isset($daily[$day])) {
                $daily[$day] = [
                    'sales' => 0,
                    'profit' => 0,
                ];
            }

            $sales = 0;
            $cost = 0;

            /*
             * الطلبات الجديدة متعددة المنتجات
             */
            if ($order->items->isNotEmpty()) {

                foreach ($order->items as $item) {

                    $sales +=
                        ($item->price ?? 0) * $item->quantity;

                    $cost +=
                        ($item->cost ?? 0) * $item->quantity;
                }

            } else {

                /*
                 * الطلبات القديمة
                 */
                $sales =
                    ($order->price ?? 0) *
                    ($order->quantity ?? 0);

                $cost =
                    ($order->cost ?? 0) *
                    ($order->quantity ?? 0);
            }

            $daily[$day]['sales'] += $sales;
            $daily[$day]['profit'] += $sales - $cost;
        }

        /*
         * تجهيز بيانات الرسم البياني
         * لآخر 7 أيام.
         */
        $chart = [
            'labels' => [],
            'sales' => [],
            'profit' => [],
        ];

        for ($i = 6; $i >= 0; $i--) {

            $date = now()->subDays($i);
            $day = $date->format('Y-m-d');

            $chart['labels'][] = $date->format('m/d');

            $chart['sales'][] =
                isset($daily[$day])
                    ? (float) $daily[$day]['sales']
                    : 0;

            $chart['profit'][] =
                isset($daily[$day])
                    ? (float) $daily[$day]['profit']
                    : 0;
        }

        /*
         * المنتجات منخفضة المخزون
         */
        $lowStockProducts = $user->products()
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        /*
         * أحدث 5 طلبات
         */
        $recentOrders = $user->orders()
            ->with(['customer', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        /*
         * عدد الطلبات حسب الحالة
         */
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
