
<x-app-layout>
    @php
        $locale = auth()->user()?->locale ?? app()->getLocale();
        $isEnglish = $locale === 'en';

        $statusLabels = [
            'جديد' => $isEnglish ? 'New' : 'جديد',
            'قيد التجهيز' => $isEnglish ? 'Processing' : 'قيد التجهيز',
            'تم الشحن' => $isEnglish ? 'Shipped' : 'تم الشحن',
            'تم التسليم' => $isEnglish ? 'Delivered' : 'تم التسليم',
            'ملغي' => $isEnglish ? 'Cancelled' : 'ملغي',
        ];
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="plus" class="w-5 h-5 text-neon" />
            {{ $isEnglish ? 'New Order' : 'طلب جديد' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 bg-surface border border-line rounded p-6">

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

            <form method="POST"
                  action="{{ route('orders.store') }}"
                  class="space-y-5"
                  x-data="orderForm()"
                  @submit="submitting = true">

                @csrf

                {{-- Customer --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">
                        {{ $isEnglish ? 'Customer' : 'العميل' }}
                    </label>

                    <select name="customer_id"
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                        <option value="">
                            {{ $isEnglish ? '-- Select existing customer --' : '-- اختر عميل موجود --' }}
                        </option>

                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    @selected(old('customer_id') == $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-sm text-muted">
                    {{ $isEnglish ? 'Or add a new customer now:' : 'أو أضف عميل جديد الآن:' }}
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text"
                           name="new_customer_name"
                           value="{{ old('new_customer_name') }}"
                           placeholder="{{ $isEnglish ? 'New customer name' : 'اسم العميل الجديد' }}"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <div class="flex gap-2">
                        <select name="new_customer_country"
                                class="bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon w-20 px-2">
                            <option value="970" @selected(old('new_customer_country', '970') == '970')>🇵🇸 970</option>
                            <option value="966" @selected(old('new_customer_country') == '966')>🇸🇦 966</option>
                            <option value="971" @selected(old('new_customer_country') == '971')>🇦🇪 971</option>
                            <option value="965" @selected(old('new_customer_country') == '965')>🇰🇼 965</option>
                            <option value="974" @selected(old('new_customer_country') == '974')>🇶🇦 974</option>
                            <option value="973" @selected(old('new_customer_country') == '973')>🇧🇭 973</option>
                            <option value="968" @selected(old('new_customer_country') == '968')>🇴🇲 968</option>
                            <option value="962" @selected(old('new_customer_country') == '962')>🇯🇴 962</option>
                            <option value="20" @selected(old('new_customer_country') == '20')>🇪🇬 20</option>
                        </select>

                        <input type="text"
                               name="new_customer_phone"
                               value="{{ old('new_customer_phone') }}"
                               placeholder="{{ $isEnglish ? '5XXXXXXXX (without leading zero)' : '5XXXXXXXX (بدون صفر)' }}"
                               class="flex-1 min-w-0 bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    </div>
                </div>

                {{-- Products --}}
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-3 gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-ink">
                                {{ $isEnglish ? 'Products' : 'المنتجات' }}
                            </h3>
                            <p class="text-sm text-muted">
                                {{ $isEnglish ? 'Add one or more products to this order.' : 'أضف منتجًا واحدًا أو أكثر إلى الطلب.' }}
                            </p>
                        </div>

                        <button type="button"
                                @click="addItem()"
                                class="bg-neon hover:bg-neon-dim text-night px-3 py-2 rounded font-semibold flex items-center gap-2 shrink-0">
                            <x-icon name="plus" class="w-4 h-4" />
                            {{ $isEnglish ? 'Add Product' : 'إضافة منتج' }}
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="item.key">
                            <div class="p-4 rounded border border-line bg-surface2">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-ink">
                                        {{ $isEnglish ? 'Product' : 'المنتج' }}
                                        <span x-text="index + 1"></span>
                                    </span>

                                    <button type="button"
                                            x-show="items.length > 1"
                                            @click="removeItem(index)"
                                            class="text-red-400 hover:text-red-300 text-sm">
                                        {{ $isEnglish ? 'Remove' : 'حذف' }}
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                    {{-- Product selection --}}
                                    <div class="md:col-span-5">
                                        <label class="block mb-1 text-sm text-muted">
                                            {{ $isEnglish ? 'Product' : 'المنتج' }}
                                        </label>

                                        <select :name="`items[${index}][product_id]`"
                                                x-model="item.product_id"
                                                @change="selectProduct(index)"
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon">
                                            <option value="">
                                                {{ $isEnglish ? '-- Enter manually --' : '-- اكتب يدويًا --' }}
                                            </option>

                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }} — {{ $isEnglish ? 'Stock:' : 'المخزون:' }} {{ $product->stock }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Product name --}}
                                    <div class="md:col-span-4">
                                        <label class="block mb-1 text-sm text-muted">
                                            {{ $isEnglish ? 'Product / Description' : 'المنتج / الوصف' }}
                                        </label>

                                        <input type="text"
                                               :name="`items[${index}][item]`"
                                               x-model="item.item"
                                               required
                                               class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                               placeholder="{{ $isEnglish ? 'Product name' : 'اسم المنتج' }}">
                                    </div>

                                    {{-- Quantity --}}
                                    <div class="md:col-span-3">
                                        <label class="block mb-1 text-sm text-muted">
                                            {{ $isEnglish ? 'Quantity' : 'الكمية' }}
                                        </label>

                                        <input type="number"
                                               :name="`items[${index}][quantity]`"
                                               x-model.number="item.quantity"
                                               min="1"
                                               :max="item.product_id && item.stock > 0 ? item.stock : null"
                                               required
                                               class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon">

                                        <p x-show="item.product_id && item.stock > 0"
                                           class="mt-1 text-xs text-muted">
                                            {{ $isEnglish ? 'Available:' : 'المتاح:' }}
                                            <span x-text="item.stock"></span>
                                        </p>

                                        <p x-show="item.product_id && item.stock === 0"
                                           class="mt-1 text-xs text-red-400">
                                            {{ $isEnglish ? 'This product is out of stock.' : 'هذا المنتج غير متوفر.' }}
                                        </p>
                                    </div>

                                    {{-- Unit cost --}}
                                    <div class="md:col-span-4">
                                        <label class="block mb-1 text-sm text-muted">
                                            {{ $isEnglish ? 'Unit Cost' : 'التكلفة للقطعة' }}
                                        </label>

                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               :name="`items[${index}][cost]`"
                                               x-model="item.cost"
                                               class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon">
                                    </div>

                                    {{-- Unit selling price --}}
                                    <div class="md:col-span-4">
                                        <label class="block mb-1 text-sm text-muted">
                                            {{ $isEnglish ? 'Unit Selling Price' : 'سعر البيع للقطعة' }}
                                        </label>

                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               :name="`items[${index}][price]`"
                                               x-model="item.price"
                                               class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon">
                                    </div>

                                    {{-- Product profit --}}
                                    <div class="md:col-span-4">
                                        <label class="block mb-1 text-sm text-muted">
                                            {{ $isEnglish ? 'Net Profit' : 'صافي الربح' }}
                                        </label>

                                        <div class="w-full bg-surface border border-line rounded px-3 py-2 font-bold"
                                             :class="itemProfit(item) >= 0 ? 'text-neon' : 'text-red-400'"
                                             x-text="itemProfit(item).toFixed(2)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Order summary --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-4 rounded border border-line bg-surface2">
                        <div class="text-sm text-muted mb-1">
                            {{ $isEnglish ? 'Total Sales' : 'إجمالي المبيعات' }}
                        </div>
                        <div class="text-xl font-bold text-ink"
                             x-text="totalRevenue().toFixed(2)"></div>
                    </div>

                    <div class="p-4 rounded border border-line bg-surface2">
                        <div class="text-sm text-muted mb-1">
                            {{ $isEnglish ? 'Total Cost' : 'إجمالي التكلفة' }}
                        </div>
                        <div class="text-xl font-bold text-ink"
                             x-text="totalCost().toFixed(2)"></div>
                    </div>

                    <div class="p-4 rounded border border-line bg-surface2">
                        <div class="text-sm text-muted mb-1">
                            {{ $isEnglish ? 'Net Profit' : 'صافي الربح' }}
                        </div>
                        <div class="text-xl font-bold"
                             :class="totalProfit() >= 0 ? 'text-neon' : 'text-red-400'"
                             x-text="totalProfit().toFixed(2)"></div>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">
                        {{ $isEnglish ? 'Status' : 'الحالة' }}
                    </label>

                    <select name="status"
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}"
                                    @selected(old('status', 'جديد') === $status)>
                                {{ $statusLabels[$status] ?? $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">
                        {{ $isEnglish ? 'Notes' : 'ملاحظات' }}
                    </label>

                    <textarea name="notes"
                              rows="3"
                              class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">{{ old('notes') }}</textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-2 flex-wrap">
                    <button type="submit"
                            :disabled="submitting || hasStockError()"
                            :class="submitting || hasStockError() ? 'opacity-50 cursor-not-allowed' : ''"
                            class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold flex items-center gap-2">
                        <x-icon name="check-circle" />
                        <span x-text="submitting
                            ? @js($isEnglish ? 'Saving...' : 'جاري الحفظ...')
                            : @js($isEnglish ? 'Save Order' : 'حفظ الطلب')"></span>
                    </button>

                    <a href="{{ route('orders.index') }}"
                       class="px-4 py-2 rounded border border-line text-muted hover:text-ink">
                        {{ $isEnglish ? 'Cancel' : 'إلغاء' }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function orderForm() {
            return {
                submitting: false,

                products: @json($products),

                items: [
                    {
                        key: Date.now(),
                        product_id: '',
                        item: '',
                        quantity: 1,
                        price: '',
                        cost: '',
                        stock: 0,
                    }
                ],

                addItem() {
                    this.items.push({
                        key: Date.now() + Math.random(),
                        product_id: '',
                        item: '',
                        quantity: 1,
                        price: '',
                        cost: '',
                        stock: 0,
                    });
                },

                removeItem(index) {
                    if (this.items.length === 1) {
                        return;
                    }

                    this.items.splice(index, 1);
                },

                selectProduct(index) {
                    const currentItem = this.items[index];

                    const product = this.products.find(
                        product => product.id == currentItem.product_id
                    );

                    if (!product) {
                        currentItem.item = '';
                        currentItem.price = '';
                        currentItem.cost = '';
                        currentItem.stock = 0;
                        currentItem.quantity = 1;

                        return;
                    }

                    currentItem.item = product.name;
                    currentItem.price = product.price ?? '';
                    currentItem.cost = product.cost ?? '';
                    currentItem.stock = Number(product.stock ?? 0);

                    if (
                        currentItem.stock > 0 &&
                        Number(currentItem.quantity) > currentItem.stock
                    ) {
                        currentItem.quantity = currentItem.stock;
                    }

                    if (currentItem.stock === 0) {
                        currentItem.quantity = 1;
                    }
                },

                itemProfit(item) {
                    const price = Number(item.price || 0);
                    const cost = Number(item.cost || 0);
                    const quantity = Number(item.quantity || 0);

                    return (price - cost) * quantity;
                },

                totalRevenue() {
                    return this.items.reduce((total, item) => {
                        return total +
                            (Number(item.price || 0) *
                             Number(item.quantity || 0));
                    }, 0);
                },

                totalCost() {
                    return this.items.reduce((total, item) => {
                        return total +
                            (Number(item.cost || 0) *
                             Number(item.quantity || 0));
                    }, 0);
                },

                totalProfit() {
                    return this.totalRevenue() - this.totalCost();
                },

                hasStockError() {
                    return this.items.some(item => {
                        return item.product_id &&
                               Number(item.stock) === 0;
                    });
                }
            }
        }
    </script>
</x-app-layout>
