<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="pencil" class="w-5 h-5 text-neon" />
            تعديل الطلب
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-400/10 text-red-300 rounded border border-red-400/20">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('stock_error'))
                <div class="mb-4 p-3 bg-red-400/10 text-red-300 rounded border border-red-400/20">
                    {{ session('stock_error') }}
                </div>
            @endif

            <div
                class="bg-surface border border-line rounded p-6"
                x-data="orderEditForm()"
            >

                <form method="POST"
                      action="{{ route('orders.update', $order) }}"
                      class="space-y-6">

                    @csrf
                    @method('PUT')

                    {{-- العميل --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">
                            العميل
                        </label>

                        <select
                            name="customer_id"
                            required
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
                        >
                            @foreach ($customers as $customer)
                                <option
                                    value="{{ $customer->id }}"
                                    @selected(
                                        old('customer_id', $order->customer_id) == $customer->id
                                    )
                                >
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- المنتجات --}}
                    <div>

                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-base font-semibold text-ink">
                                    منتجات الطلب
                                </h3>

                                <p class="text-xs text-muted mt-1">
                                    يمكنك تعديل الكمية أو إضافة وحذف المنتجات.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="addItem()"
                                class="bg-neon hover:bg-neon-dim text-night px-3 py-2 rounded font-semibold flex items-center gap-2"
                            >
                                <x-icon name="plus" />
                                إضافة منتج
                            </button>
                        </div>

                        <div class="space-y-4">

                            <template
                                x-for="(item, index) in items"
                                :key="item.key"
                            >

                                <div class="bg-surface2 border border-line rounded-lg p-4">

                                    <div class="flex justify-between items-center mb-4">

                                        <span class="text-sm font-semibold text-ink">
                                            المنتج
                                            <span x-text="index + 1"></span>
                                        </span>

                                        <button
                                            type="button"
                                            x-show="items.length > 1"
                                            @click="removeItem(index)"
                                            class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1"
                                        >
                                            <x-icon name="trash" class="w-4 h-4" />
                                            حذف
                                        </button>

                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        {{-- اختيار المنتج --}}
                                        <div class="md:col-span-2">

                                            <label class="block mb-1 text-sm font-medium text-ink">
                                                اختر من منتجاتك
                                            </label>

                                            <select
                                                :name="`items[${index}][product_id]`"
                                                x-model="item.product_id"
                                                @change="selectProduct(index)"
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >

                                                <option value="">
                                                    -- اكتب المنتج يدويًا --
                                                </option>

                                                @foreach ($products as $product)
                                                    <option
                                                        value="{{ $product->id }}"
                                                    >
                                                        {{ $product->name }}
                                                        — المخزون: {{ $product->stock }}
                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>

                                        {{-- المخزون --}}
                                        <div
                                            x-show="item.product_id !== ''"
                                            x-cloak
                                            class="md:col-span-2 p-3 rounded border border-line bg-surface flex justify-between items-center"
                                        >

                                            <span class="text-muted">
                                                المخزون المتاح حاليًا
                                            </span>

                                            <span
                                                class="font-bold"
                                                :class="item.stock > 0 ? 'text-neon' : 'text-red-400'"
                                                x-text="item.stock"
                                            ></span>

                                        </div>

                                        {{-- اسم المنتج --}}
                                        <div class="md:col-span-2">

                                            <label class="block mb-1 text-sm font-medium text-ink">
                                                المنتج / الوصف
                                            </label>

                                            <input
                                                type="text"
                                                :name="`items[${index}][item]`"
                                                x-model="item.item"
                                                required
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >

                                        </div>

                                        {{-- الكمية --}}
                                        <div>

                                            <label class="block mb-1 text-sm font-medium text-ink">
                                                الكمية
                                            </label>

                                            <input
                                                type="number"
                                                :name="`items[${index}][quantity]`"
                                                x-model.number="item.quantity"
                                                min="1"
                                                :max="item.product_id !== '' && item.stock > 0 ? item.stock : null"
                                                required
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >

                                            <p
                                                x-show="item.product_id !== '' && item.stock > 0"
                                                class="mt-1 text-xs text-muted"
                                            >
                                                الحد الأقصى المتاح:
                                                <span x-text="item.stock"></span>
                                            </p>

                                        </div>

                                        {{-- التكلفة --}}
                                        <div>

                                            <label class="block mb-1 text-sm font-medium text-ink">
                                                سعر المنتج
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                :name="`items[${index}][cost]`"
                                                x-model="item.cost"
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >

                                        </div>

                                        {{-- سعر البيع --}}
                                        <div>

                                            <label class="block mb-1 text-sm font-medium text-ink">
                                                سعر البيع
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                :name="`items[${index}][price]`"
                                                x-model="item.price"
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >

                                        </div>

                                        {{-- ربح المنتج --}}
                                        <div>

                                            <label class="block mb-1 text-sm font-medium text-ink">
                                                الربح
                                            </label>

                                            <div
                                                class="w-full bg-surface border border-line rounded px-3 py-2 font-semibold"
                                                :class="itemProfit(item) >= 0 ? 'text-neon' : 'text-red-400'"
                                                x-text="itemProfit(item).toFixed(2)"
                                            ></div>

                                        </div>

                                    </div>

                                </div>

                            </template>

                        </div>

                    </div>

                    {{-- ملخص الطلب --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                        <div class="bg-surface2 border border-line rounded-lg p-4">
                            <div class="text-sm text-muted">
                                إجمالي البيع
                            </div>

                            <div class="text-xl font-bold text-ink mt-1"
                                 x-text="totalRevenue().toFixed(2)">
                            </div>
                        </div>

                        <div class="bg-surface2 border border-line rounded-lg p-4">
                            <div class="text-sm text-muted">
                                إجمالي التكلفة
                            </div>

                            <div class="text-xl font-bold text-ink mt-1"
                                 x-text="totalCost().toFixed(2)">
                            </div>
                        </div>

                        <div class="bg-surface2 border border-line rounded-lg p-4">
                            <div class="text-sm text-muted">
                                صافي الربح
                            </div>

                            <div
                                class="text-xl font-bold mt-1"
                                :class="totalProfit() >= 0 ? 'text-neon' : 'text-red-400'"
                                x-text="totalProfit().toFixed(2)"
                            ></div>
                        </div>

                    </div>

                    {{-- الحالة --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">
                            الحالة
                        </label>

                        <select
                            name="status"
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
                        >

                            @foreach ($statuses as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected(old('status', $order->status) === $status)
                                >
                                    {{ $status }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- الملاحظات --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">
                            ملاحظات
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
                        >{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    {{-- الأزرار --}}
                    <div class="flex gap-2 justify-between flex-wrap">

                        <div class="flex gap-2">

                            <button
                                type="submit"
                                class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold flex items-center gap-2"
                            >
                                <x-icon name="check-circle" />
                                حفظ التعديلات
                            </button>

                            <a
                                href="{{ route('orders.index') }}"
                                class="px-4 py-2 rounded border border-line text-muted hover:text-ink"
                            >
                                إلغاء
                            </a>

                        </div>

                        <button
                            type="submit"
                            form="delete-order"
                            class="text-red-400 hover:text-red-300 px-4 py-2 flex items-center gap-1"
                        >
                            <x-icon name="trash" />
                            حذف الطلب
                        </button>

                    </div>

                </form>

                {{-- حذف الطلب --}}
                <form
                    id="delete-order"
                    method="POST"
                    action="{{ route('orders.destroy', $order) }}"
                    onsubmit="return confirm('متأكد من حذف الطلب؟')"
                >
                    @csrf
                    @method('DELETE')
                </form>

            </div>

        </div>
    </div>

    <script>
        function orderEditForm() {
            return {
                products: @json($products),

              items: @js(
    old(
        'items',
        $order->items->map(function ($item) {
            return [
                'key' => uniqid(),
                'product_id' => $item->product_id,
                'item' => $item->item,
                'quantity' => $item->quantity,
                'cost' => $item->cost,
                'price' => $item->price,
                'stock' => $item->product?->stock ?? 0,
            ];
        })->values()->toArray()
    )
),

                init() {
                    if (!this.items.length) {
                        this.items = [{
                            key: Date.now(),
                            product_id: '',
                            item: @json($order->item),
                            quantity: @json($order->quantity),
                            cost: @json($order->cost),
                            price: @json($order->price),
                            stock: 0,
                        }];
                    }
                },

                addItem() {
                    this.items.push({
                        key: Date.now() + Math.random(),
                        product_id: '',
                        item: '',
                        quantity: 1,
                        cost: '',
                        price: '',
                        stock: 0,
                    });
                },

                removeItem(index) {
                    if (this.items.length <= 1) {
                        return;
                    }

                    this.items.splice(index, 1);
                },

                selectProduct(index) {
                    const item = this.items[index];

                    const product = this.products.find(
                        product => product.id == item.product_id
                    );

                    if (product) {
                        item.item = product.name;
                        item.cost = product.cost ?? '';
                        item.price = product.price ?? '';
                        item.stock = Number(product.stock ?? 0);

                        if (Number(item.quantity) > item.stock) {
                            item.quantity = item.stock > 0
                                ? item.stock
                                : 1;
                        }
                    } else {
                        item.stock = 0;
                    }
                },

                itemProfit(item) {
                    return (
                        (Number(item.price || 0) -
                        Number(item.cost || 0)) *
                        Number(item.quantity || 0)
                    );
                },

                totalRevenue() {
                    return this.items.reduce((total, item) => {
                        return total +
                            Number(item.price || 0) *
                            Number(item.quantity || 0);
                    }, 0);
                },

                totalCost() {
                    return this.items.reduce((total, item) => {
                        return total +
                            Number(item.cost || 0) *
                            Number(item.quantity || 0);
                    }, 0);
                },

                totalProfit() {
                    return this.totalRevenue() - this.totalCost();
                }
            }
        }
    </script>

</x-app-layout>
