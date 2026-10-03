<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('customer')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        $totals = $request->user()->orders()
            ->where('status', '!=', 'ملغي')
            ->where('created_at', '>=', now()->startOfMonth())
            ->selectRaw('COALESCE(SUM(price * quantity), 0) as revenue, COALESCE(SUM(cost * quantity), 0) as cost')
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
            'customers' => $request->user()->customers()->orderBy('name')->get(),
            'products' => $request->user()->products()->orderBy('name')->get(),
            'statuses' => Order::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateOrder($request);

        if (! $request->filled('customer_id') && ! $request->filled('new_customer_name')) {
            return back()->withErrors(['customer_id' => 'اختر عميل موجود أو أدخل اسم عميل جديد.'])->withInput();
        }

        if ($request->filled('new_customer_name')) {
            $customer = $request->user()->customers()->create([
                'name' => $request->new_customer_name,
                'phone' => $request->new_customer_phone,
            ]);
            $data['customer_id'] = $customer->id;
        }

        $request->user()->orders()->create($data);

        return redirect()->route('orders.index')->with('status', 'تم إضافة الطلب بنجاح.');
    }

    public function edit(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('orders.edit', [
            'order' => $order,
            'customers' => $request->user()->customers()->orderBy('name')->get(),
            'products' => $request->user()->products()->orderBy('name')->get(),
            'statuses' => Order::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->update($this->validateOrder($request, requireCustomer: true));

        return redirect()->route('orders.index')->with('status', 'تم تحديث الطلب.');
    }

    public function destroy(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->delete();

        return redirect()->route('orders.index')->with('status', 'تم حذف الطلب.');
    }
 public function invoice(Request $request, Order $order)
{
    abort_unless($order->user_id === $request->user()->id, 403);

    return view('orders.invoice', ['order' => $order->load('customer')]);
}

    public function report(Request $request)
    {
        $daily = $request->user()->orders()
            ->where('status', '!=', 'ملغي')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(price * quantity), 0) as revenue, COALESCE(SUM(cost * quantity), 0) as cost')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $chart = ['labels' => [], 'revenue' => [], 'profit' => []];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $row = $daily->get($date->format('Y-m-d'));
            $chart['labels'][] = $date->format('m/d');
            $chart['revenue'][] = $row ? (float) $row->revenue : 0;
            $chart['profit'][] = $row ? (float) $row->revenue - (float) $row->cost : 0;
        }

        return view('reports.index', ['chart' => $chart]);
    }

    private function validateOrder(Request $request, bool $requireCustomer = false): array
    {
        return $request->validate([
            'customer_id' => $requireCustomer ? ['required', 'exists:customers,id'] : ['nullable', 'exists:customers,id'],
            'new_customer_name' => ['nullable', 'string', 'max:255'],
            'new_customer_phone' => ['nullable', 'string', 'max:50'],
            'item' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:' . implode(',', Order::STATUSES)],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
