<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return view('products.index', [
            'products' => $request->user()->products()->orderBy('name')->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
       $data = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'cost' => ['nullable', 'numeric', 'min:0'],
    'price' => ['nullable', 'numeric', 'min:0'],
    'stock' => ['required', 'integer', 'min:0'],
]);

        $request->user()->products()->create($data);

        return redirect()->route('products.index')->with('status', 'تم إضافة المنتج.');
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->user_id === $request->user()->id, 403);

       $data = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'cost' => ['nullable', 'numeric', 'min:0'],
    'price' => ['nullable', 'numeric', 'min:0'],
    'stock' => ['required', 'integer', 'min:0'],
]);

        $product->update($data);

        return redirect()->route('products.index')->with('status', 'تم تحديث المنتج.');
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->user_id === $request->user()->id, 403);

        $product->delete();

        return redirect()->route('products.index')->with('status', 'تم حذف المنتج.');
    }
}
