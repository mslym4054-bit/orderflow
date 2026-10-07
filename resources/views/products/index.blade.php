<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="box" class="w-5 h-5 text-neon" /> المنتجات
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- الرسائل --}}
            @if (session('status'))
                <div class="p-3 bg-neon/10 text-neon rounded border border-neon/20">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-red-400/10 text-red-300 rounded border border-red-400/20">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- إضافة منتج --}}
            <div class="bg-surface border border-line rounded p-6">
                <h3 class="font-semibold mb-3 text-ink">
                    إضافة منتج جديد
                </h3>

                <form method="POST"
                      action="{{ route('products.store') }}"
                      class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">

                    @csrf

                    <input type="text"
                           name="name"
                           placeholder="اسم المنتج"
                           required
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <input type="number"
                           step="0.01"
                           name="cost"
                           placeholder="سعر المنتج"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <input type="number"
                           name="stock"
                           min="0"
                           value="0"
                           placeholder="المخزون"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <input type="number"
                           step="0.01"
                           name="price"
                           placeholder="سعر البيع"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <button type="submit"
                            class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold">
                        إضافة
                    </button>

                </form>
            </div>

            {{-- المنتجات --}}
            <div class="bg-surface border border-line rounded overflow-x-auto">

                <table class="w-full text-sm text-right">

                    <thead class="bg-surface2">
                        <tr>
                            <th class="p-3 text-muted font-medium">الاسم</th>
                            <th class="p-3 text-muted font-medium">سعر المنتج</th>
                            <th class="p-3 text-muted font-medium">سعر البيع</th>
                            <th class="p-3 text-muted font-medium">المخزون</th>
                            <th class="p-3 text-muted font-medium">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($products as $product)

                            <tr class="border-t border-line">

                                <td class="p-3 text-ink">
                                    {{ $product->name }}
                                </td>

                                <td class="p-3 text-ink">
                                    {{ $product->cost !== null ? number_format($product->cost, 2) : '-' }}
                                </td>

                                <td class="p-3 text-ink">
                                    {{ $product->price !== null ? number_format($product->price, 2) : '-' }}
                                </td>

                                <td class="p-3">

                                    @if ($product->stock <= 0)

                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-400/10 text-red-400 border border-red-400/20">
                                            🔴 نفد المخزون
                                        </span>

                                    @elseif ($product->stock <= 5)

                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-400/10 text-yellow-400 border border-yellow-400/20">
                                            🟠 منخفض: {{ $product->stock }}
                                        </span>

                                    @else

                                        <span class="text-neon font-semibold">
                                            {{ $product->stock }}
                                        </span>

                                    @endif

                                </td>

                                <td class="p-3">

                                    <div class="flex items-center gap-3">

                                        {{-- تعديل --}}
                                        <details class="relative">

                                            <summary class="cursor-pointer list-none text-neon hover:text-neon-dim text-xs">
                                                تعديل
                                            </summary>

                                            <div class="absolute left-0 mt-2 z-20 w-80 bg-surface border border-line rounded-lg shadow-xl p-4">

                                                <form method="POST"
                                                      action="{{ route('products.update', $product) }}"
                                                      class="space-y-3">

                                                    @csrf
                                                    @method('PUT')

                                                    <input type="text"
                                                           name="name"
                                                           value="{{ $product->name }}"
                                                           required
                                                           placeholder="اسم المنتج"
                                                           class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                                                    <div class="grid grid-cols-2 gap-2">

                                                        <input type="number"
                                                               step="0.01"
                                                               name="cost"
                                                               value="{{ $product->cost }}"
                                                               placeholder="سعر المنتج"
                                                               class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                                                        <input type="number"
                                                               step="0.01"
                                                               name="price"
                                                               value="{{ $product->price }}"
                                                               placeholder="سعر البيع"
                                                               class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                                                    </div>

                                                    <input type="number"
                                                           name="stock"
                                                           min="0"
                                                           value="{{ $product->stock }}"
                                                           placeholder="المخزون"
                                                           class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                                                    <div class="flex gap-2">

                                                        <button type="submit"
                                                                class="bg-neon hover:bg-neon-dim text-night px-3 py-2 rounded text-xs font-semibold">
                                                            حفظ
                                                        </button>

                                                        <button type="button"
                                                                onclick="this.closest('details').removeAttribute('open')"
                                                                class="px-3 py-2 rounded border border-line text-muted hover:text-ink text-xs">
                                                            إلغاء
                                                        </button>

                                                    </div>

                                                </form>

                                            </div>

                                        </details>

                                        {{-- حذف --}}
                                        <form method="POST"
                                              action="{{ route('products.destroy', $product) }}"
                                              onsubmit="return confirm('حذف المنتج؟')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="text-red-400 hover:text-red-300 text-xs flex items-center gap-1">
                                                <x-icon name="trash" /> حذف
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="p-8 text-center text-muted">
                                    <x-icon name="folder" class="w-8 h-8 mx-auto mb-2" />
                                    لا يوجد منتجات بعد.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div>
                {{ $products->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
