<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['customer', 'items.product'])
            ->when(
                $request->status,
                fn ($q) => $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        $orders->getCollection()->transform(function ($order) {
            $items = $order->items;

            if ($items->isEmpty()) {
                $revenue = ($order->price ?? 0) * ($order->quantity ?? 0);
                $cost = ($order->cost ?? 0) * ($order->quantity ?? 0);

                $order->display_revenue = $revenue;
                $order->display_cost = $cost;
                $order->display_profit = $revenue - $cost;
                $order->display_quantity = $order->quantity ?? 0;

                return $order;
            }

            $revenue = $items->sum(
                fn ($item) => ($item->price ?? 0) * $item->quantity
            );

            $cost = $items->sum(
                fn ($item) => ($item->cost ?? 0) * $item->quantity
            );

            $order->display_revenue = $revenue;
            $order->display_cost = $cost;
            $order->display_profit = $revenue - $cost;
            $order->display_quantity = $items->sum('quantity');

            return $order;
        });

        $totals = $request->user()->orders()
            ->where('status', '!=', 'ملغي')
            ->where('created_at', '>=', now()->startOfMonth())
            ->with('items')
            ->get();

        $revenue = 0;
        $cost = 0;

        foreach ($totals as $order) {
            $items = $order->items;

            if ($items->isEmpty()) {
                $revenue += ($order->price ?? 0) * ($order->quantity ?? 0);
                $cost += ($order->cost ?? 0) * ($order->quantity ?? 0);

                continue;
            }

            foreach ($items as $item) {
                $revenue += ($item->price ?? 0) * $item->quantity;
                $cost += ($item->cost ?? 0) * $item->quantity;
            }
        }

        $stats = [
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $revenue - $cost,
        ];

        return view('orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'currentStatus' => $request->status,
            'stats' => $stats,
        ]);
    }

    public function create(Request $request)
    {
        return view('orders.create', [
            'customers' => $request->user()
                ->customers()
                ->orderBy('name')
                ->get(),

            'products' => $request->user()
                ->products()
                ->orderBy('name')
                ->get(),

            'statuses' => Order::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateOrder($request);

        if (
            ! $request->filled('customer_id') &&
            ! $request->filled('new_customer_name')
        ) {
            return back()
                ->withErrors([
                    'customer_id' => 'اختر عميلًا موجودًا أو أدخل اسم عميل جديد.',
                ])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $data) {
                /*
                 * إنشاء العميل داخل المعاملة، حتى يتم التراجع عنه
                 * إذا فشل إنشاء الطلب.
                 */
                $customerId = $data['customer_id'] ?? null;

                if ($request->filled('new_customer_name')) {
                    $phone = $request->new_customer_phone
                        ? $request->new_customer_country
                            . ltrim($request->new_customer_phone, '0')
                        : null;

                    $customer = $request->user()->customers()->create([
                        'name' => $data['new_customer_name'],
                        'phone' => $phone,
                    ]);

                    $customerId = $customer->id;
                }

                $items = [];

                foreach ($data['items'] as $itemData) {
                    $product = null;

                    if (! empty($itemData['product_id'])) {
                        $product = $request->user()
                            ->products()
                            ->lockForUpdate()
                            ->findOrFail($itemData['product_id']);

                        /*
                         * الطلبات الملغاة لا تخصم من المخزون.
                         */
                        if ($data['status'] !== 'ملغي') {
                            if ($product->stock < $itemData['quantity']) {
                                throw new \Exception(
                                    'المخزون غير كافٍ للمنتج: '
                                    . $product->name
                                    . '. المخزون المتاح حاليًا: '
                                    . $product->stock
                                );
                            }

                            $product->decrement(
                                'stock',
                                $itemData['quantity']
                            );
                        }

                        $itemData['item'] = $product->name;
                        $itemData['cost'] = $product->cost;
                        $itemData['price'] = $product->price;
                    }

                    $items[] = [
                        'product_id' => $product?->id,
                        'item' => $itemData['item'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'] ?? null,
                        'cost' => $itemData['cost'] ?? null,
                    ];
                }

                /*
                 * الحفاظ على الحقول القديمة في جدول orders.
                 */
                $firstItem = $items[0];

                $order = $request->user()->orders()->create([
                    'customer_id' => $customerId,
                    'item' => $firstItem['item'],
                    'quantity' => $firstItem['quantity'],
                    'price' => $firstItem['price'],
                    'cost' => $firstItem['cost'],
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($items as $item) {
                    $order->items()->create($item);
                }
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('stock_error', $e->getMessage());
        }

        return redirect()
            ->route('orders.index')
            ->with('status', 'تمت إضافة الطلب بنجاح.');
    }

    public function edit(Request $request, Order $order)
    {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        $order->load('items.product');

        return view('orders.edit', [
            'order' => $order,

            'customers' => $request->user()
                ->customers()
                ->orderBy('name')
                ->get(),

            'products' => $request->user()
                ->products()
                ->orderBy('name')
                ->get(),

            'statuses' => Order::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        $data = $this->validateOrder(
            $request,
            requireCustomer: true
        );

        try {
            DB::transaction(function () use ($request, $order, $data) {
                $oldItems = $order->items()->get();

                /*
                 * إعادة مخزون المنتجات المرتبطة بالطلب القديم،
                 * بشرط أن يكون الطلب القديم غير ملغى.
                 */
                foreach ($oldItems as $oldItem) {
                    if (
                        $order->status !== 'ملغي' &&
                        $oldItem->product_id
                    ) {
                        $oldProduct = $request->user()
                            ->products()
                            ->lockForUpdate()
                            ->find($oldItem->product_id);

                        if ($oldProduct) {
                            $oldProduct->increment(
                                'stock',
                                $oldItem->quantity
                            );
                        }
                    }
                }

                /*
                 * توافق مع الطلبات القديمة التي لا تحتوي على order_items.
                 * لا نربط المنتج بالاسم إلا إذا كان الاسم فريدًا.
                 */
                if (
                    $oldItems->isEmpty() &&
                    $order->status !== 'ملغي' &&
                    $order->item
                ) {
                    $matchingProducts = $request->user()
                        ->products()
                        ->where('name', $order->item)
                        ->limit(2)
                        ->get();

                    if ($matchingProducts->count() === 1) {
                        $oldProduct = $request->user()
                            ->products()
                            ->lockForUpdate()
                            ->find($matchingProducts->first()->id);

                        if ($oldProduct) {
                            $oldProduct->increment(
                                'stock',
                                $order->quantity
                            );
                        }
                    }
                }

                $newItems = [];

                foreach ($data['items'] as $itemData) {
                    $product = null;

                    if (! empty($itemData['product_id'])) {
                        $product = $request->user()
                            ->products()
                            ->lockForUpdate()
                            ->findOrFail($itemData['product_id']);

                        /*
                         * فحص الكمية وخصم المخزون فقط للطلب الفعال.
                         */
                        if ($data['status'] !== 'ملغي') {
                            if ($product->stock < $itemData['quantity']) {
                                throw new \Exception(
                                    'المخزون غير كافٍ للمنتج: '
                                    . $product->name
                                    . '. المخزون المتاح حاليًا: '
                                    . $product->stock
                                );
                            }
                        }

                        $itemData['item'] = $product->name;
                        $itemData['cost'] = $product->cost;
                        $itemData['price'] = $product->price;

                        if ($data['status'] !== 'ملغي') {
                            $product->decrement(
                                'stock',
                                $itemData['quantity']
                            );
                        }
                    }

                    $newItems[] = [
                        'product_id' => $product?->id,
                        'item' => $itemData['item'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'] ?? null,
                        'cost' => $itemData['cost'] ?? null,
                    ];
                }

                $firstItem = $newItems[0];

                $order->update([
                    'customer_id' => $data['customer_id'],
                    'item' => $firstItem['item'],
                    'quantity' => $firstItem['quantity'],
                    'price' => $firstItem['price'],
                    'cost' => $firstItem['cost'],
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]);

                $order->items()->delete();

                foreach ($newItems as $item) {
                    $order->items()->create($item);
                }
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('stock_error', $e->getMessage());
        }

        return redirect()
            ->route('orders.index')
            ->with('status', 'تم تحديث الطلب.');
    }

    public function destroy(Request $request, Order $order)
    {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        DB::transaction(function () use ($request, $order) {
            $items = $order->items()->get();

            if ($order->status !== 'ملغي') {
                foreach ($items as $item) {
                    if (! $item->product_id) {
                        continue;
                    }

                    $product = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->find($item->product_id);

                    if ($product) {
                        $product->increment(
                            'stock',
                            $item->quantity
                        );
                    }
                }
            }

            /*
             * الطلبات القديمة التي لا تحتوي على order_items.
             * لا نستخدم الاسم إذا كان غير فريد.
             */
            if (
                $items->isEmpty() &&
                $order->status !== 'ملغي' &&
                $order->item
            ) {
                $matchingProducts = $request->user()
                    ->products()
                    ->where('name', $order->item)
                    ->limit(2)
                    ->get();

                if ($matchingProducts->count() === 1) {
                    $product = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->find($matchingProducts->first()->id);

                    if ($product) {
                        $product->increment(
                            'stock',
                            $order->quantity
                        );
                    }
                }
            }

            $order->delete();
        });

        return redirect()
            ->route('orders.index')
            ->with('status', 'تم حذف الطلب.');
    }

    public function invoice(Request $request, Order $order)
    {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        $order->load([
            'customer',
            'user',
            'items.product',
        ]);

        return view('orders.invoice', [
            'order' => $order,
        ]);
    }

    public function report(Request $request)
    {
        $user = $request->user();

        $startDate = now()->startOfMonth()->subMonths(11);
        $endDate = now()->endOfMonth();

        $orders = $user->orders()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('items.product')
            ->orderBy('created_at')
            ->get();

        $monthRecords = [];
        $productSales = [];

        $chart = [
            'labels' => [],
            'revenue' => [],
            'profit' => [],
        ];

        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');

        $stats = [
            'revenue' => 0,
            'cost' => 0,
            'profit' => 0,
            'orders' => 0,
            'quantity' => 0,
        ];

        $previousStats = [
            'revenue' => 0,
            'profit' => 0,
            'orders' => 0,
        ];

        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $key = $date->format('Y-m');

            $monthRecords[$key] = [
                'key' => $key,
                'month' => $date->format('m/Y'),
                'revenue' => 0,
                'cost' => 0,
                'profit' => 0,
                'orders' => 0,
                'quantity' => 0,
            ];
        }

        foreach ($orders as $order) {
            if ($order->status === 'ملغي') {
                continue;
            }

            $key = $order->created_at->format('Y-m');

            if (! isset($monthRecords[$key])) {
                continue;
            }

            $revenue = 0;
            $cost = 0;
            $quantity = 0;

            if ($order->items->isNotEmpty()) {
                foreach ($order->items as $item) {
                    $itemQuantity = (int) $item->quantity;

                    $itemRevenue = (float) ($item->price ?? 0)
                        * $itemQuantity;

                    $itemCost = (float) ($item->cost ?? 0)
                        * $itemQuantity;

                    $revenue += $itemRevenue;
                    $cost += $itemCost;
                    $quantity += $itemQuantity;

                    $name = $item->item ?: 'منتج بدون اسم';

                    if (! isset($productSales[$name])) {
                        $productSales[$name] = [
                            'name' => $name,
                            'quantity' => 0,
                            'revenue' => 0,
                        ];
                    }

                    $productSales[$name]['quantity'] += $itemQuantity;
                    $productSales[$name]['revenue'] += $itemRevenue;
                }
            } else {
                $quantity = (int) ($order->quantity ?? 0);

                $revenue = (float) ($order->price ?? 0) * $quantity;
                $cost = (float) ($order->cost ?? 0) * $quantity;

                $name = $order->item ?: 'منتج بدون اسم';

                if (! isset($productSales[$name])) {
                    $productSales[$name] = [
                        'name' => $name,
                        'quantity' => 0,
                        'revenue' => 0,
                    ];
                }

                $productSales[$name]['quantity'] += $quantity;
                $productSales[$name]['revenue'] += $revenue;
            }

            $profit = $revenue - $cost;

            $monthRecords[$key]['revenue'] += $revenue;
            $monthRecords[$key]['cost'] += $cost;
            $monthRecords[$key]['profit'] += $profit;
            $monthRecords[$key]['orders']++;
            $monthRecords[$key]['quantity'] += $quantity;

            if ($key === $currentMonth) {
                $stats['revenue'] += $revenue;
                $stats['cost'] += $cost;
                $stats['profit'] += $profit;
                $stats['orders']++;
                $stats['quantity'] += $quantity;
            }

            if ($key === $previousMonth) {
                $previousStats['revenue'] += $revenue;
                $previousStats['profit'] += $profit;
                $previousStats['orders']++;
            }
        }

        foreach ($monthRecords as $record) {
            $chart['labels'][] = $record['month'];
            $chart['revenue'][] = round($record['revenue'], 2);
            $chart['profit'][] = round($record['profit'], 2);
        }

        $topProducts = collect($productSales)
            ->sortByDesc('quantity')
            ->take(5)
            ->values();

        $statusCounts = $user->orders()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('reports.index', [
            'chart' => $chart,
            'statusCounts' => $statusCounts,
            'stats' => $stats,
            'previousStats' => $previousStats,
            'monthlyRecords' => collect($monthRecords)
                ->reverse()
                ->values(),
            'topProducts' => $topProducts,
        ]);
    }

    private function validateOrder(
        Request $request,
        bool $requireCustomer = false
    ): array {
        return $request->validate([
            'customer_id' => [
                $requireCustomer ? 'required' : 'nullable',
                Rule::exists('customers', 'id')
                    ->where(
                        fn ($query) => $query->where(
                            'user_id',
                            $request->user()->id
                        )
                    ),
            ],

            'new_customer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'new_customer_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'new_customer_country' => [
                'nullable',
                'string',
                'max:10',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'nullable',
                Rule::exists('products', 'id')
                    ->where(
                        fn ($query) => $query->where(
                            'user_id',
                            $request->user()->id
                        )
                    ),
            ],

            'items.*.item' => [
                'required',
                'string',
                'max:255',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'items.*.cost' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:' . implode(',', Order::STATUSES),
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }
}
