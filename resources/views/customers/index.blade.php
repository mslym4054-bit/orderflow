<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="users" class="w-5 h-5 text-neon" />
            العملاء
        </h2>
    </x-slot>

```
<div class="py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- رسائل النجاح --}}
        @if (session('status'))
            <div class="p-3 bg-neon/10 text-neon rounded border border-neon/20">
                {{ session('status') }}
            </div>
        @endif

        {{-- أخطاء التحقق --}}
        @if ($errors->any())
            <div class="p-4 bg-red-500/10 text-red-400 rounded border border-red-500/20">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- إضافة عميل --}}
        <div class="bg-surface border border-line rounded-lg p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon name="plus" class="w-5 h-5 text-neon" />
                <h3 class="font-semibold text-ink">
                    إضافة عميل جديد
                </h3>
            </div>

            <form
                method="POST"
                action="{{ route('customers.store') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end"
            >
                @csrf

                {{-- الاسم --}}
                <div>
                    <label class="block text-xs text-muted mb-1">
                        الاسم
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="اسم العميل"
                        required
                        class="w-full bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon"
                    >
                </div>

                {{-- الدولة --}}
                <div>
                    <label class="block text-xs text-muted mb-1">
                        الدولة
                    </label>

                    <select
                        name="country"
                        class="w-full bg-surface2 border-line text-ink rounded focus:border-neon focus:ring-neon"
                    >
                        <option value="970">🇵🇸 فلسطين +970</option>
                        <option value="966">🇸🇦 السعودية +966</option>
                        <option value="971">🇦🇪 الإمارات +971</option>
                        <option value="965">🇰🇼 الكويت +965</option>
                        <option value="974">🇶🇦 قطر +974</option>
                        <option value="973">🇧🇭 البحرين +973</option>
                        <option value="968">🇴🇲 عمان +968</option>
                        <option value="962">🇯🇴 الأردن +962</option>
                        <option value="20">🇪🇬 مصر +20</option>
                    </select>
                </div>

                {{-- الجوال --}}
                <div>
                    <label class="block text-xs text-muted mb-1">
                        الجوال
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="5XXXXXXXX"
                        class="w-full bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon"
                    >
                </div>

                {{-- زر الإضافة --}}
                <button
                    type="submit"
                    class="bg-neon hover:bg-neon-dim text-night px-4 py-2.5 rounded font-semibold transition"
                >
                    إضافة العميل
                </button>
            </form>

            {{-- الملاحظات --}}
            <div class="mt-3">
                <label class="block text-xs text-muted mb-1">
                    ملاحظات
                </label>

                <textarea
                    name="notes"
                    form="add-customer-form"
                    rows="2"
                    placeholder="ملاحظات عن العميل..."
                    class="w-full bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon"
                >{{ old('notes') }}</textarea>
            </div>
        </div>


        {{-- قائمة العملاء --}}
        <div class="bg-surface border border-line rounded-lg overflow-hidden">

            <div class="p-4 border-b border-line flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-ink">
                        قائمة العملاء
                    </h3>

                    <p class="text-xs text-muted mt-1">
                        {{ number_format($customers->total(), 0, '.', ',') }}
                        عميل
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">

                    <thead class="bg-surface2">
                        <tr>
                            <th class="p-3 text-muted font-medium">
                                الاسم
                            </th>

                            <th class="p-3 text-muted font-medium">
                                الجوال
                            </th>

                            <th class="p-3 text-muted font-medium">
                                عدد الطلبات
                            </th>

                            <th class="p-3 text-muted font-medium">
                                الملاحظات
                            </th>

                            <th class="p-3 text-muted font-medium text-center">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($customers as $customer)

                            <tr class="border-t border-line hover:bg-surface2/50 transition">

                                {{-- الاسم --}}
                                <td class="p-3 text-ink font-medium">
                                    {{ $customer->name }}
                                </td>

                                {{-- الجوال --}}
                                <td class="p-3">

                                    @if ($customer->phone)

                                        <a
                                            href="https://wa.me/{{ preg_replace('/\D/', '', $customer->phone) }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="text-neon hover:text-neon-dim inline-flex items-center gap-1"
                                        >
                                            <x-icon name="chat" class="w-4 h-4" />
                                            {{ $customer->phone }}
                                        </a>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>

                                {{-- الطلبات --}}
                                <td class="p-3 text-ink">
                                    {{ number_format($customer->orders_count, 0, '.', ',') }}
                                </td>

                                {{-- الملاحظات --}}
                                <td class="p-3 text-muted max-w-xs">
                                    @if ($customer->notes)
                                        <span title="{{ $customer->notes }}">
                                            {{ \Illuminate\Support\Str::limit($customer->notes, 40) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- الإجراءات --}}
                                <td class="p-3">

                                    <div class="flex items-center justify-center gap-3">

                                        {{-- تعديل --}}
                                        <button
                                            type="button"
                                            onclick="document.getElementById('edit-customer-{{ $customer->id }}').classList.toggle('hidden')"
                                            class="text-neon hover:text-neon-dim text-xs inline-flex items-center gap-1"
                                        >
                                            <x-icon name="pencil" />
                                            تعديل
                                        </button>

                                        {{-- حذف --}}
                                        <form
                                            method="POST"
                                            action="{{ route('customers.destroy', $customer) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف العميل؟')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="text-red-400 hover:text-red-300 text-xs inline-flex items-center gap-1"
                                            >
                                                <x-icon name="trash" />
                                                حذف
                                            </button>
                                        </form>

                                    </div>

                                </td>
                            </tr>


                            {{-- نموذج التعديل --}}
                            <tr
                                id="edit-customer-{{ $customer->id }}"
                                class="hidden bg-surface2 border-t border-line"
                            >
                                <td colspan="5" class="p-5">

                                   <form
                                id="add-customer-form"
                                method="POST"
                                action="{{ route('customers.store') }}"
                                class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end"
                            >
                                        @csrf
                                        @method('PUT')

                                        {{-- الاسم --}}
                                        <div>
                                            <label class="block text-xs text-muted mb-1">
                                                الاسم
                                            </label>

                                            <input
                                                type="text"
                                                name="name"
                                                value="{{ $customer->name }}"
                                                required
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >
                                        </div>

                                        {{-- الدولة --}}
                                        <div>
                                            <label class="block text-xs text-muted mb-1">
                                                الدولة
                                            </label>

                                            @php
                                                $phone = $customer->phone ?? '';

                                                $country = '';

                                                foreach ([
                                                    '970',
                                                    '966',
                                                    '971',
                                                    '965',
                                                    '974',
                                                    '973',
                                                    '968',
                                                    '962',
                                                    '20'
                                                ] as $code) {
                                                    if (str_starts_with($phone, $code)) {
                                                        $country = $code;
                                                        break;
                                                    }
                                                }
                                            @endphp

                                            <select
                                                name="country"
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >
                                                <option value="970" @selected($country === '970')>🇵🇸 فلسطين +970</option>
                                                <option value="966" @selected($country === '966')>🇸🇦 السعودية +966</option>
                                                <option value="971" @selected($country === '971')>🇦🇪 الإمارات +971</option>
                                                <option value="965" @selected($country === '965')>🇰🇼 الكويت +965</option>
                                                <option value="974" @selected($country === '974')>🇶🇦 قطر +974</option>
                                                <option value="973" @selected($country === '973')>🇧🇭 البحرين +973</option>
                                                <option value="968" @selected($country === '968')>🇴🇲 عمان +968</option>
                                                <option value="962" @selected($country === '962')>🇯🇴 الأردن +962</option>
                                                <option value="20" @selected($country === '20')>🇪🇬 مصر +20</option>
                                            </select>
                                        </div>

                                        {{-- الجوال --}}
                                        <div>
                                            <label class="block text-xs text-muted mb-1">
                                                الجوال
                                            </label>

                                            <input
                                                type="text"
                                                name="phone"
                                                value="{{ preg_replace('/^\d{2,3}/', '', $phone) }}"
                                                placeholder="5XXXXXXXX"
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >
                                        </div>

                                        {{-- الحفظ --}}
                                        <div class="flex gap-2">

                                            <button
                                                type="submit"
                                                class="bg-neon hover:bg-neon-dim text-night px-4 py-2.5 rounded font-semibold"
                                            >
                                                حفظ
                                            </button>

                                            <button
                                                type="button"
                                                onclick="document.getElementById('edit-customer-{{ $customer->id }}').classList.add('hidden')"
                                                class="bg-surface border border-line text-muted hover:text-ink px-4 py-2.5 rounded"
                                            >
                                                إلغاء
                                            </button>

                                        </div>

                                        {{-- الملاحظات --}}
                                        <div class="md:col-span-4">

                                            <label class="block text-xs text-muted mb-1">
                                                ملاحظات
                                            </label>

                                            <textarea
                                                name="notes"
                                                rows="2"
                                                class="w-full bg-surface border-line text-ink rounded focus:border-neon focus:ring-neon"
                                            >{{ $customer->notes }}</textarea>

                                        </div>

                                    </form>

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="p-10 text-center text-muted"
                                >
                                    <x-icon
                                        name="folder"
                                        class="w-8 h-8 mx-auto mb-2"
                                    />

                                    لا يوجد عملاء بعد.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div>
            {{ $customers->links() }}
        </div>

    </div>
</div>


</x-app-layout>
