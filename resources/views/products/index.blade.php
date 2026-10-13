
<x-app-layout>
    @php
        $locale = auth()->user()?->locale ?? app()->getLocale();
        $isEnglish = $locale === 'en';

        $labels = [
            'title' => $isEnglish ? 'Products' : 'المنتجات',
            'add_product' => $isEnglish ? 'Add New Product' : 'إضافة منتج جديد',
            'name' => $isEnglish ? 'Product Name' : 'اسم المنتج',
            'cost' => $isEnglish ? 'Cost Price' : 'سعر المنتج',
            'price' => $isEnglish ? 'Selling Price' : 'سعر البيع',
            'stock' => $isEnglish ? 'Stock' : 'المخزون',
            'actions' => $isEnglish ? 'Actions' : 'الإجراءات',
            'add' => $isEnglish ? 'Add' : 'إضافة',
            'edit' => $isEnglish ? 'Edit' : 'تعديل',
            'save' => $isEnglish ? 'Save' : 'حفظ',
            'cancel' => $isEnglish ? 'Cancel' : 'إلغاء',
            'delete' => $isEnglish ? 'Delete' : 'حذف',
            'delete_confirm' => $isEnglish ? 'Delete this product?' : 'هل تريد حذف المنتج؟',
            'out_of_stock' => $isEnglish ? 'Out of stock' : 'نفد المخزون',
            'low_stock' => $isEnglish ? 'Low stock' : 'مخزون منخفض',
            'empty' => $isEnglish ? 'No products yet.' : 'لا يوجد منتجات بعد.',
        ];
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="box" class="w-5 h-5 text-neon" />
            {{ $labels['title'] }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status message --}}
            @if (session('status'))
                <div class="p-3 bg-neon/10 text-neon rounded border border-neon/20">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="p-3 bg-red-400/10 text-red-300 rounded border border-red-400/20">
                    <ul class="list-disc {{ $isEnglish ? 'pl-5' : 'pr-5' }}">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Add product --}}
            <div class="bg-surface border border-line rounded p-6">
                <h3 class="font-semibold mb-3 text-ink">
                    {{ $labels['add_product'] }}
                </h3>

                <form method="POST"
                      action="{{ route('products.store') }}"
                      class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    @csrf

                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="{{ $labels['name'] }}"
                           required
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="cost"
                           value="{{ old('cost') }}"
                           placeholder="{{ $labels['cost'] }}"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <input type="number"
                           name="stock"
                           min="0"
                           value="{{ old('stock', 0) }}"
                           placeholder="{{ $labels['stock'] }}"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="price"
                           value="{{ old('price') }}"
                           placeholder="{{ $labels['price'] }}"
                           class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">

                    <button type="submit"
                            class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold">
                        {{ $labels['add'] }}
                    </button>
                </form>
            </div>

            {{-- Products table --}}
            <div class="bg-surface border border-line rounded overflow-x-auto">
                <table class="w-full text-sm {{ $isEnglish ? 'text-left' : 'text-right' }}">
                    <thead class="bg-surface2">
                        <tr>
                            <th class="p-3 text-muted font-medium">{{ $labels['name'] }}</th>
                            <th class="p-3 text-muted font-medium">{{ $labels['cost'] }}</th>
                            <th class="p-3 text-muted font-medium">{{ $labels['price'] }}</th>
                            <th class="p-3 text-muted font-medium">{{ $labels['stock'] }}</th>
                            <th class="p-3 text-muted font-medium">{{ $labels['actions'] }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-t border-line">
                                <td class="p-3 text-ink">
                                    {{ $product->name }}
                                </td>

                                <td class="p-3 text-ink">
                                    {{ $product->cost !== null ? number_format((float) $product->cost, 2, '.', ',') : '-' }}
                                </td>

                                <td class="p-3 text-ink">
                                    {{ $product->price !== null ? number_format((float) $product->price, 2, '.', ',') : '-' }}
                                </td>

                                <td class="p-3">
                                    @if ($product->stock <= 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-400/10 text-red-400 border border-red-400/20">
                                            🔴 {{ $labels['out_of_stock'] }}
                                        </span>
                                    @elseif ($product->stock <= 5)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-400/10 text-yellow-400 border border-yellow-400/20">
                                            🟠 {{ $labels['low_stock'] }}:
                                            {{ number_format((int) $product->stock, 0, '.', ',') }}
                                        </span>
                                    @else
                                        <span class="text-neon font-semibold">
                                            {{ number_format((int) $product->stock, 0, '.', ',') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    <div class="flex items-center gap-3">

                                        {{-- Edit --}}
                                        <details class="relative">
                                            <summary class="cursor-pointer list-none text-neon hover:text-neon-dim text-xs">
                                                {{ $labels['edit'] }}
                                            </summary>

                                            <div class="absolute {{ $isEnglish ? 'right-0' : 'left-0' }} mt-2 z-20 w-80 max-w-[90vw] bg-surface border border-line rounded-lg shadow-xl p-4">
                                                <form method="POST"
                                                      action="{{ route('products.update', $product) }}"
                                                      class="space-y-3">
                                                    @csrf
                                                    @method('PUT')

                                                    <input type="text"
                                                           name="name"
                                                           value="{{ $product->name }}"
                                                           required
                                                           placeholder="{{ $labels['name'] }}"
                                                           class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                                                    <div class="grid grid-cols-2 gap-2">
                                                        <input type="number"
                                                               step="0.01"
                                                               min="0"
                                                               name="cost"
                                                               value="{{ $product->cost }}"
                                                               placeholder="{{ $labels['cost'] }}"
                                                               class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                                                        <input type="number"
                                                               step="0.01"
                                                               min="0"
                                                               name="price"
                                                               value="{{ $product->price }}"
                                                               placeholder="{{ $labels['price'] }}"
                                                               class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">
                                                    </div>

                                                    <input type="number"
                                                           name="stock"
                                                           min="0"
                                                           value="{{ $product->stock }}"
                                                           placeholder="{{ $labels['stock'] }}"
                                                           class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon">

                                                    <div class="flex gap-2">
                                                        <button type="submit"
                                                                class="bg-neon hover:bg-neon-dim text-night px-3 py-2 rounded text-xs font-semibold">
                                                            {{ $labels['save'] }}
                                                        </button>

                                                        <button type="button"
                                                                onclick="this.closest('details').removeAttribute('open')"
                                                                class="px-3 py-2 rounded border border-line text-muted hover:text-ink text-xs">
                                                            {{ $labels['cancel'] }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </details>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('products.destroy', $product) }}"
                                              onsubmit="return confirm(@js($labels['delete_confirm']))">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="text-red-400 hover:text-red-300 text-xs flex items-center gap-1">
                                                <x-icon name="trash" />
                                                {{ $labels['delete'] }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-muted">
                                    <x-icon name="folder" class="w-8 h-8 mx-auto mb-2" />
                                    {{ $labels['empty'] }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-line">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</x-app-layout>

