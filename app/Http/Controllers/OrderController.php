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

        // الطلبات القديمة التي لا تحتوي على order_items
        if ($items->isEmpty()) {
            $revenue = ($order->price ?? 0) * ($order->quantity ?? 0);
            $cost = ($order->cost ?? 0) * ($order->quantity ?? 0);

            $order->display_revenue = $revenue;
            $order->display_cost = $cost;
            $order->display_profit = $revenue - $cost;
            $order->display_quantity = $order->quantity ?? 0;

            return $order;
        }

        $revenue = $items->sum(function ($item) {
            return ($item->price ?? 0) * $item->quantity;
        });

        $cost = $items->sum(function ($item) {
            return ($item->cost ?? 0) * $item->quantity;
        });

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

        // الطلبات القديمة
        if ($items->isEmpty()) {
            $revenue += ($order->price ?? 0) * ($order->quantity ?? 0);
            $cost += ($order->cost ?? 0) * ($order->quantity ?? 0);
            continue;
        }

        // الطلبات الجديدة متعددة المنتجات
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
                'customer_id' => 'اختر عميل موجود أو أدخل اسم عميل جديد.'
            ])
            ->withInput();
    }

    if ($request->filled('new_customer_name')) {
        $phone = $request->new_customer_phone
            ? $request->new_customer_country
                . ltrim($request->new_customer_phone, '0')
            : null;

        $customer = $request->user()->customers()->create([
            'name' => $request->new_customer_name,
            'phone' => $phone,
        ]);

        $data['customer_id'] = $customer->id;
    }

    try {
        $order = DB::transaction(function () use ($request, $data) {

            $items = [];

            foreach ($data['items'] as $itemData) {

                $product = null;

                if (! empty($itemData['product_id'])) {
                    $product = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->findOrFail($itemData['product_id']);

                    if ($product->stock < $itemData['quantity']) {
                        throw new \Exception(
                            '⚠️ المخزون غير كافٍ للمنتج: '
                            . $product->name
                            . '. المخزون المتاح حاليًا: '
                            . $product->stock
                        );
                    }

                    $itemData['item'] = $product->name;
                    $itemData['cost'] = $product->cost;
                    $itemData['price'] = $product->price;

                    $product->decrement(
                        'stock',
                        $itemData['quantity']
                    );
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
             * نحافظ على الحقول القديمة في orders
             * حتى لا نكسر الطلبات القديمة أو الأجزاء الحالية من النظام.
             *
             * نضع بيانات أول عنصر فقط في الحقول القديمة.
             */
            $firstItem = $items[0];

            $orderData = [
                'customer_id' => $data['customer_id'] ?? null,
                'item' => $firstItem['item'],
                'quantity' => $firstItem['quantity'],
                'price' => $firstItem['price'],
                'cost' => $firstItem['cost'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ];

            $order = $request->user()
                ->orders()
                ->create($orderData);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            return $order;
        });
    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('stock_error', $e->getMessage());
    }

    return redirect()
        ->route('orders.index')
        ->with('status', 'تم إضافة الطلب بنجاح.');
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

            /*
             * أولًا: نجمع بيانات الطلب القديم
             * حتى نعيد مخزونه قبل تطبيق المنتجات الجديدة.
             */
            $oldItems = $order->items()->get();

            /*
             * إذا كان الطلب قديمًا ولا يحتوي على order_items،
             * نحافظ على بياناته القديمة ونتعامل معه كمنتج واحد.
             */


            /*
             * إعادة مخزون المنتجات القديمة.
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
             * الطلبات القديمة التي لا تحتوي على order_items.
             * نعيد مخزون المنتج القديم إن وجد.
             */
            if ($oldItems->isEmpty() && $order->status !== 'ملغي') {

                $oldProductId = null;

                if ($order->item) {
                    $oldProductId = $request->user()
                        ->products()
                        ->where('name', $order->item)
                        ->value('id');
                }

                if ($oldProductId) {
                    $oldProduct = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->find($oldProductId);

                    if ($oldProduct) {
                        $oldProduct->increment(
                            'stock',
                            $order->quantity
                        );
                    }
                }
            }

            /*
             * تجهيز المنتجات الجديدة.
             */
            $newItems = [];

            foreach ($data['items'] as $itemData) {

                $product = null;

                if (! empty($itemData['product_id'])) {

                    $product = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->findOrFail($itemData['product_id']);

                    /*
                     * إذا كان الطلب الجديد غير ملغي،
                     * يجب التأكد من توفر المخزون.
                     */
                    if (
                        $data['status'] !== 'ملغي' &&
                        $product->stock < $itemData['quantity']
                    ) {
                        throw new \Exception(
                            '⚠️ المخزون غير كافٍ للمنتج: '
                            . $product->name
                            . '. المخزون المتاح حاليًا: '
                            . $product->stock
                        );
                    }

                    $itemData['item'] = $product->name;
                    $itemData['cost'] = $product->cost;
                    $itemData['price'] = $product->price;

                    /*
                     * خصم المخزون فقط إذا كان الطلب فعالًا.
                     */
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

            /*
             * الحفاظ على الحقول القديمة في orders
             * باستخدام أول عنصر.
             */
            $firstItem = $newItems[0];

            $orderData = [
                'customer_id' => $data['customer_id'],
                'item' => $firstItem['item'],
                'quantity' => $firstItem['quantity'],
                'price' => $firstItem['price'],
                'cost' => $firstItem['cost'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ];

            $order->update($orderData);

            /*
             * حذف العناصر القديمة وإعادة إنشاء العناصر الجديدة.
             *
             * هذا أبسط وأضمن في هذه المرحلة،
             * خصوصًا أن عدد المنتجات داخل الطلب قد يتغير.
             */
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

        /*
         * الطلبات الجديدة متعددة المنتجات.
         * نرجع مخزون كل منتج قبل حذف الطلب.
         */
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
         * نحافظ على التوافق معها.
         */
        if (
            $items->isEmpty() &&
            $order->status !== 'ملغي' &&
            $order->item
        ) {

            $product = $request->user()
                ->products()
                ->lockForUpdate()
                ->where('name', $order->item)
                ->first();

            if ($product) {
                $product->increment(
                    'stock',
                    $order->quantity
                );
            }
        }

        /*
         * حذف الطلب.
         * سيتم حذف order_items تلقائيًا بسبب cascadeOnDelete.
         */
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
    $orders = $request->user()->orders()
        ->where('status', '!=', 'ملغي')
        ->where(
            'created_at',
            '>=',
            now()->subDays(13)->startOfDay()
        )
        ->with('items')
        ->get();

    $chart = [
        'labels' => [],
        'revenue' => [],
        'profit' => [],
    ];

    for ($i = 13; $i >= 0; $i--) {

        $date = now()->subDays($i);

        $dayOrders = $orders->filter(function ($order) use ($date) {
            return $order->created_at->isSameDay($date);
        });

        $revenue = 0;
        $cost = 0;

        foreach ($dayOrders as $order) {

            /*
             * الطلبات الجديدة متعددة المنتجات
             */
            if ($order->items->isNotEmpty()) {

                foreach ($order->items as $item) {

                    $revenue += ($item->price ?? 0) * $item->quantity;
                    $cost += ($item->cost ?? 0) * $item->quantity;
                }

                continue;
            }

            /*
             * الطلبات القديمة التي لا تحتوي على order_items
             */
            $revenue += ($order->price ?? 0) * ($order->quantity ?? 0);
            $cost += ($order->cost ?? 0) * ($order->quantity ?? 0);
        }

        $chart['labels'][] = $date->format('m/d');
        $chart['revenue'][] = $revenue;
        $chart['profit'][] = $revenue - $cost;
    }

    /*
     * توزيع الطلبات حسب الحالة
     */
    $statusCounts = $request->user()
        ->orders()
        ->selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();

    return view('reports.index', [
        'chart' => $chart,
        'statusCounts' => $statusCounts,
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
