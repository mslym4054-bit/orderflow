<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = $request->user()->customers()
            ->withCount('orders')
            ->orderBy('name')
            ->paginate(15);

        return view('customers.index', ['customers' => $customers]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $request->user()->customers()->create($data);

        return redirect()->route('customers.index')->with('status', 'تم إضافة العميل.');
    }

    public function update(Request $request, Customer $customer)
    {
        abort_unless($customer->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('status', 'تم تحديث العميل.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        abort_unless($customer->user_id === $request->user()->id, 403);

        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'تم حذف العميل.');
    }
}
