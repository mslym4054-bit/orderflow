<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="plus" class="w-5 h-5 text-neon" /> طلب جديد
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

            <form method="POST"
      action="{{ route('orders.store') }}"
      class="space-y-4"
      x-data="{
                      price: {{ Illuminate\Support\Js::from(old('price')) }},
                      cost: {{ Illuminate\Support\Js::from(old('cost')) }},
                      qty: {{ Illuminate\Support\Js::from(old('quantity', 1)) }},
                      item: {{ Illuminate\Support\Js::from(old('item')) }},
                      productId: {{ Illuminate\Support\Js::from(old('product_id')) }},
                      stock: 0,
                      products: {{ Illuminate\Support\Js::from($products) }},
                      submitting: false,

                      selectProduct(value) {
                          const p = this.products.find(p => p.id == value);

                          if (p) {
                              this.productId = p.id;
                              this.item = p.name;
                              this.cost = p.cost ?? '';
                              this.price = p.price ?? '';
                              this.stock = Number(p.stock ?? 0);

                              if (this.stock > 0 && Number(this.qty) > this.stock) {
                                  this.qty = this.stock;
                              }

                              if (this.stock === 0) {
                                  this.qty = 1;
                              }
                          } else {
                              this.productId = '';
                              this.stock = 0;
                          }
                      }
                  }"
                  x-init="if (productId) selectProduct(productId)"
                  @submit="submitting = true">

                @csrf

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">العميل</label>

                    <select name="customer_id"
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                        <option value="">-- اختر عميل موجود --</option>

                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    @selected(old('customer_id') == $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-sm text-muted">
                    أو أضف عميل جديد الآن:
                </div>

                <div class="grid grid-cols-2 gap-3">

                    <input type="text"
                           name="new_customer_name"
                           value="{{ old('new_customer_name') }}"
                           placeholder="اسم العميل الجديد"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <div class="flex gap-2">

                        <select name="new_customer_country"
                                class="bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon w-20 px-2">
                            <option value="970">🇵🇸 970</option>
                            <option value="966">🇸🇦 966</option>
                            <option value="971">🇦🇪 971</option>
                            <option value="965">🇰🇼 965</option>
                            <option value="974">🇶🇦 974</option>
                            <option value="973">🇧🇭 973</option>
                            <option value="968">🇴🇲 968</option>
                            <option value="962">🇯🇴 962</option>
                            <option value="20">🇪🇬 20</option>
                        </select>

                        <input type="text"
                               name="new_customer_phone"
                               value="{{ old('new_customer_phone') }}"
                               placeholder="5XXXXXXXX (بدون صفر)"
                               class="flex-1 bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    </div>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">
                        اختر من منتجاتك (اختياري)
                    </label>

                    <select name="product_id"
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
                            @change="selectProduct($event.target.value)">

                        <option value="">-- اكتب يدويًا --</option>

                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} — المخزون: {{ $product->stock }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="productId !== ''"
                     x-cloak
                     class="p-3 rounded border border-line bg-surface2 flex justify-between items-center">

                    <span class="text-muted">
                        المخزون المتاح
                    </span>

                    <span class="font-bold"
                          :class="stock > 0 ? 'text-neon' : 'text-red-400'"
                          x-text="stock">
                    </span>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">
                        المنتج / الوصف
                    </label>

                    <input type="text"
                           name="item"
                           value="{{ old('item') }}"
                           required
                           class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
                           x-model="item">
                </div>

                <div class="grid grid-cols-3 gap-3">

                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">
                            الكمية
                        </label>

                        <input type="number"
                               name="quantity"
                               value="{{ old('quantity', 1) }}"
                               min="1"
                               :max="productId !== '' && stock > 0 ? stock : null"
                               required
                               class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
                               x-model="qty">

                        <p x-show="productId !== '' && stock > 0"
                           class="mt-1 text-xs text-muted">
                            الحد الأقصى المتاح:
                            <span x-text="stock"></span>
                        </p>

                        <p x-show="productId !== '' && stock === 0"
                           class="mt-1 text-xs text-red-400">
                            هذا المنتج غير متوفر في المخزون.
                        </p>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">
                            سعر المنتج (للقطعة)
                        </label>

                        <input type="number"
                               step="0.01"
                               name="cost"
                               x-model="cost"
                               class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-ink">
                            سعر البيع (للقطعة)
                        </label>

                        <input type="number"
                               step="0.01"
                               name="price"
                               x-model="price"
                               class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                    </div>

                </div>

                <div class="p-3 rounded border border-line bg-surface2 flex justify-between items-center">

                    <span class="text-muted">
                        صافي الربح
                    </span>

                    <span class="font-bold text-lg"
                          :class="((Number(price || 0) - Number(cost || 0)) * Number(qty || 0)) >= 0
                              ? 'text-neon'
                              : 'text-red-400'"
                          x-text="((Number(price || 0) - Number(cost || 0)) * Number(qty || 0)).toFixed(2)">
                    </span>

                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">
                        الحالة
                    </label>

                    <select name="status"
                            class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                        @foreach ($statuses as $status)
                            <option value="{{ $status }}"
                                    @selected(old('status') === $status)>
                                {{ $status }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-ink">
                        ملاحظات
                    </label>

                    <textarea name="notes"
                              class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-2">

                    <button type="submit"
                            :disabled="submitting || (productId !== '' && stock === 0)"
                            :class="submitting || (productId !== '' && stock === 0)
                                ? 'opacity-50 cursor-not-allowed'
                                : ''"
                            class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold flex items-center gap-2">

                        <x-icon name="check-circle" />

                        <span x-text="submitting ? 'جاري الحفظ...' : 'حفظ الطلب'"></span>

                    </button>

                    <a href="{{ route('orders.index') }}"
                       class="px-4 py-2 rounded border border-line text-muted hover:text-ink">
                        إلغاء
                    </a>

                </div>

            </form>
        </div>
    </div>
</x-app-layout>
