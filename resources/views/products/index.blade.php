<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="box" class="w-5 h-5 text-neon" /> المنتجات
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-3 bg-neon/10 text-neon rounded border border-neon/20">{{ session('status') }}</div>
            @endif

            <div class="bg-surface border border-line rounded p-6">
                <h3 class="font-semibold mb-3 text-ink">إضافة منتج جديد</h3>
                <form method="POST" action="{{ route('products.store') }}" class="grid grid-cols-4 gap-3 items-end">
                    @csrf
                    <input type="text" name="name" placeholder="اسم المنتج" required class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    <input type="number" step="0.01" name="cost" placeholder="سعر المنتج" class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    <input type="number" step="0.01" name="price" placeholder="سعر البيع" class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    <button class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold">إضافة</button>
                </form>
            </div>

            <div class="bg-surface border border-line rounded overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-surface2">
                        <tr>
                            <th class="p-3 text-muted font-medium">الاسم</th>
                            <th class="p-3 text-muted font-medium">سعر المنتج</th>
                            <th class="p-3 text-muted font-medium">سعر البيع</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-t border-line">
                                <td class="p-3 text-ink">{{ $product->name }}</td>
                                <td class="p-3 text-ink">{{ $product->cost !== null ? number_format($product->cost, 2) : '-' }}</td>
                                <td class="p-3 text-ink">{{ $product->price !== null ? number_format($product->price, 2) : '-' }}</td>
                                <td class="p-3">
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('حذف المنتج؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-400 hover:text-red-300 text-xs flex items-center gap-1">
                                            <x-icon name="trash" /> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-8 text-center text-muted">
                                <x-icon name="folder" class="w-8 h-8 mx-auto mb-2" /> لا يوجد منتجات بعد.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
