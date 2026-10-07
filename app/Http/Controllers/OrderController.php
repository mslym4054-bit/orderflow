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

        $totals = $request->user()->orders()
            ->where('status', '!=', 'ملغي')
            ->where('created_at', '>=', now()->startOfMonth())
            ->selectRaw(
                'COALESCE(SUM(price * quantity), 0) as revenue,
                 COALESCE(SUM(cost * quantity), 0) as cost'
            )
            ->first();

        $stats = [
            'revenue' => (float) $totals->revenue,
            'cost' => (float) $totals->cost,
            'profit' => (float) $totals->revenue - (float) $totals->cost,
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

        if ($request->filled('product_id')) {

            $product = $request->user()
                ->products()
                ->findOrFail($request->product_id);

            $data['item'] = $product->name;
            $data['cost'] = $product->cost;
            $data['price'] = $product->price;
        }

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

                if ($request->filled('product_id')) {

                    $product = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->findOrFail($request->product_id);

                    if ($product->stock < $data['quantity']) {

                        throw new \Exception(
                            '⚠️ المخزون غير كافٍ. المخزون المتاح حاليًا: '
                            . $product->stock
                        );
                    }

                    $product->decrement(
                        'stock',
                        $data['quantity']
                    );
                }

                $order = $request->user()
                    ->orders()
                    ->create($data);

                $order->items()->create([
                    'product_id' => $request->filled('product_id')
                        ? $request->product_id
                        : null,

                    'item' => $data['item'],

                    'quantity' => $data['quantity'],

                    'price' => $data['price'] ?? null,

                    'cost' => $data['cost'] ?? null,
                ]);

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

            DB::transaction(function () use (
                $request,
                $order,
                &$data
            ) {

                $oldItem = $order->items()->first();

                $oldProductId = $oldItem?->product_id;

                $oldQuantity = $oldItem?->quantity
                    ?? $order->quantity;

                $oldStatus = $order->status;

                $newProductId = $request->filled('product_id')
                    ? (int) $request->product_id
                    : null;

                $newQuantity = (int) $data['quantity'];

                $newStatus = $data['status'];

                $oldIsActive = $oldStatus !== 'ملغي';

                $newIsActive = $newStatus !== 'ملغي';


                /*
                 * إذا كان الطلب القديم غير ملغي،
                 * نرجع كميته للمخزون أولاً.
                 */

                if ($oldIsActive && $oldProductId) {

                    $oldProduct = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->findOrFail($oldProductId);

                    $oldProduct->increment(
                        'stock',
                        $oldQuantity
                    );
                }


                /*
                 * إذا كان الطلب الجديد غير ملغي،
                 * نخصم الكمية الجديدة من المخزون.
                 */

                if ($newIsActive && $newProductId) {

                    $newProduct = $request->user()
                        ->products()
                        ->lockForUpdate()
                        ->findOrFail($newProductId);

                    if ($newProduct->stock < $newQuantity) {

                        throw new \Exception(
                            '⚠️ المخزون غير كافٍ. المخزون المتاح حاليًا: '
                            . $newProduct->stock
                        );
                    }

                    $newProduct->decrement(
                        'stock',
                        $newQuantity
                    );

                    $data['item'] = $newProduct->name;

                    $data['cost'] = $newProduct->cost;

                    $data['price'] = $newProduct->price;
                }


                /*
                 * إذا تم إلغاء الطلب،
                 * نحافظ على بيانات المنتج والسعر الموجودة.
                 */

                if (! $newIsActive && $oldItem) {

                    $data['item'] = $oldItem->item;

                    $data['price'] = $oldItem->price;

                    $data['cost'] = $oldItem->cost;
                }


                // تحديث الطلب

                $order->update($data);


                // تحديث OrderItem

                if ($oldItem) {

                    $oldItem->update([
                        'product_id' => $newProductId,

                        'item' => $data['item'],

                        'quantity' => $newQuantity,

                        'price' => $data['price'] ?? null,

                        'cost' => $data['cost'] ?? null,
                    ]);

                } else {

                    $order->items()->create([
                        'product_id' => $newProductId,

                        'item' => $data['item'],

                        'quantity' => $newQuantity,

                        'price' => $data['price'] ?? null,

                        'cost' => $data['cost'] ?? null,
                    ]);
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

            $item = $order->items()->first();

            /*
             * إذا كان الطلب غير ملغي،
             * نرجع الكمية للمخزون قبل الحذف.
             */

            if (
                $order->status !== 'ملغي' &&
                $item?->product_id
            ) {

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

        return view('orders.invoice', [
            'order' => $order->load('customer')
        ]);
    }

    public function report(Request $request)
    {
        $daily = $request->user()->orders()
            ->where('status', '!=', 'ملغي')
            ->where(
                'created_at',
                '>=',
                now()->subDays(13)->startOfDay()
            )
            ->selectRaw(
                'DATE(created_at) as day,
                 COALESCE(SUM(price * quantity), 0) as revenue,
                 COALESCE(SUM(cost * quantity), 0) as cost'
            )
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $chart = [
            'labels' => [],
            'revenue' => [],
            'profit' => [],
        ];

        for ($i = 13; $i >= 0; $i--) {

            $date = now()->subDays($i);

            $row = $daily->get(
                $date->format('Y-m-d')
            );

            $chart['labels'][] = $date->format('m/d');

            $chart['revenue'][] = $row
                ? (float) $row->revenue
                : 0;

            $chart['profit'][] = $row
                ? (float) $row->revenue - (float) $row->cost
                : 0;
        }

        return view('reports.index', [
            'chart' => $chart
        ]);
    }

    private function validateOrder(
        Request $request,
        bool $requireCustomer = false
    ): array {
        return $request->validate([

            'customer_id' => [
                $requireCustomer
                    ? 'required'
                    : 'nullable',

                Rule::exists('customers', 'id')
                    ->where(
                        fn ($query) => $query->where(
                            'user_id',
                            $request->user()->id
                        )
                    ),
            ],

            'product_id' => [
                'nullable',

                Rule::exists('products', 'id')
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
                'max:255'
            ],

            'new_customer_phone' => [
                'nullable',
                'string',
                'max:50'
            ],

            'item' => [
                'required',
                'string',
                'max:255'
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1'
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'cost' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'status' => [
                'required',
                'in:' . implode(
                    ',',
                    Order::STATUSES
                )
            ],

            'notes' => [
                'nullable',
                'string'
            ],
        ]);
    }
}
