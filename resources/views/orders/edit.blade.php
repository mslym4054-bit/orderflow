<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="pencil" class="w-5 h-5 text-neon" /> تعديل الطلب
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-surface border border-line rounded p-6">

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-400/10 text-red-300 rounded border border-red-400/20">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('orders.update', $order) }}" class="space-y-4"
               x-data="{ price: '{{ old('price', $order->price) }}', cost: '{{ old('cost', $order->cost) }}', qty: '{{ old('quantity', $order->quantity) }}', item: '{{ old('item', $order->item) }}', products: @json($products) }"
                @csrf
                @method('PUT')

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">العميل</label>
                    <select name="customer_id" required class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected($order->customer_id === $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                    <div>
    <label class="block mb-1 text-sm font-medium text-ink">اختر من منتجاتك (اختياري)</label>
    <select class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
            @change="const p = products.find(p => p.id == $event.target.value); if (p) { item = p.name; cost = p.cost ?? ''; price = p.price ?? ''; }">
        <option value="">-- اكتب يدويًا --</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    </select>
</div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">المنتج / الوصف</label>
                    <input type="text" name="item" value="{{ old('item', $order->item) }}" required class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon" x-model="item">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">الكمية</label>
                        <input type="number" name="quantity" value="{{ old('quantity', $order->quantity) }}" min="1" required class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon" x-model="qty">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">سعر المنتج</label>
                        <input type="number" step="0.01" name="cost" x-model="cost" class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">سعر البيع</label>
                        <input type="number" step="0.01" name="price" x-model="price" class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                    </div>
                </div>

               <div class="p-3 rounded border border-line bg-surface2 flex justify-between items-center">
    <span class="text-muted">صافي الربح</span>
    <span class="font-bold text-lg"
          :class="((Number(price || 0) - Number(cost || 0)) * Number(qty || 0)) >= 0 ? 'text-neon' : 'text-red-400'"
          x-text="((Number(price || 0) - Number(cost || 0)) * Number(qty || 0)).toFixed(2)"></span>
</div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">الحالة</label>
                    <select name="status" class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">ملاحظات</label>
                    <textarea name="notes" class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">{{ old('notes', $order->notes) }}</textarea>
                </div>

                <div class="flex gap-2 justify-between flex-wrap">
                    <div class="flex gap-2">
                        <button type="submit" class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold flex items-center gap-2">
                            <x-icon name="check-circle" /> حفظ التعديلات
                        </button>
                        <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded border border-line text-muted hover:text-ink">إلغاء</a>
                    </div>
                    <button type="submit" form="delete-order" class="text-red-400 hover:text-red-300 px-4 py-2 flex items-center gap-1">
                        <x-icon name="trash" /> حذف الطلب
                    </button>
                </div>
            </form>

            <form id="delete-order" method="POST" action="{{ route('orders.destroy', $order) }}"
                  onsubmit="return confirm('متأكد من حذف الطلب؟')">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
