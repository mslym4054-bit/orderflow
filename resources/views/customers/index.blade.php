
<x-app-layout>
    @php
        $locale = auth()->user()?->locale ?? app()->getLocale();
        $isEnglish = $locale === 'en';

        $labels = [
            'title' => $isEnglish ? 'Customers' : 'العملاء',
            'add_customer' => $isEnglish ? 'Add New Customer' : 'إضافة عميل جديد',
            'customer_list' => $isEnglish ? 'Customer List' : 'قائمة العملاء',
            'name' => $isEnglish ? 'Name' : 'الاسم',
            'country' => $isEnglish ? 'Country' : 'الدولة',
            'phone' => $isEnglish ? 'Phone Number' : 'الجوال',
            'orders' => $isEnglish ? 'Orders' : 'عدد الطلبات',
            'notes' => $isEnglish ? 'Notes' : 'ملاحظات',
            'actions' => $isEnglish ? 'Actions' : 'الإجراءات',
            'add' => $isEnglish ? 'Add Customer' : 'إضافة العميل',
            'edit' => $isEnglish ? 'Edit' : 'تعديل',
            'save' => $isEnglish ? 'Save' : 'حفظ',
            'cancel' => $isEnglish ? 'Cancel' : 'إلغاء',
            'delete' => $isEnglish ? 'Delete' : 'حذف',
            'delete_confirm' => $isEnglish
                ? 'Are you sure you want to delete this customer?'
                : 'هل أنت متأكد من حذف العميل؟',
            'no_customers' => $isEnglish ? 'No customers yet.' : 'لا يوجد عملاء بعد.',
            'total_customers' => $isEnglish ? 'Total customers' : 'إجمالي العملاء',
            'whatsapp' => $isEnglish ? 'WhatsApp' : 'واتساب',
            'phone_placeholder' => $isEnglish ? 'Phone number without country code' : 'رقم الجوال بدون رمز الدولة',
        ];

        $countries = [
            '970' => '🇵🇸 ' . ($isEnglish ? 'Palestine' : 'فلسطين') . ' +970',
            '966' => '🇸🇦 ' . ($isEnglish ? 'Saudi Arabia' : 'السعودية') . ' +966',
            '971' => '🇦🇪 ' . ($isEnglish ? 'United Arab Emirates' : 'الإمارات') . ' +971',
            '965' => '🇰🇼 ' . ($isEnglish ? 'Kuwait' : 'الكويت') . ' +965',
            '974' => '🇶🇦 ' . ($isEnglish ? 'Qatar' : 'قطر') . ' +974',
            '973' => '🇧🇭 ' . ($isEnglish ? 'Bahrain' : 'البحرين') . ' +973',
            '968' => '🇴🇲 ' . ($isEnglish ? 'Oman' : 'عُمان') . ' +968',
            '962' => '🇯🇴 ' . ($isEnglish ? 'Jordan' : 'الأردن') . ' +962',
            '20'  => '🇪🇬 ' . ($isEnglish ? 'Egypt' : 'مصر') . ' +20',
        ];

        $getPhoneParts = function ($phone) use ($countries) {
            $phone = preg_replace('/\D+/', '', (string) $phone);
            $country = '970';

            foreach (array_keys($countries) as $code) {
                if (str_starts_with($phone, $code)) {
                    $country = $code;
                    break;
                }
            }

            $localPhone = str_starts_with($phone, $country)
                ? substr($phone, strlen($country))
                : $phone;

            return [$country, $localPhone];
        };
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="users" class="w-5 h-5 text-neon" />
            {{ $labels['title'] }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status message --}}
            @if (session('status'))
                <div class="p-3 bg-neon/10 text-neon rounded border border-neon/20">
                    {{ $isEnglish ? (
                        session('status') === 'تم إضافة العميل.' ? 'Customer added successfully.' :
                        (session('status') === 'تم تحديث العميل.' ? 'Customer updated successfully.' :
                        (session('status') === 'تم حذف العميل.' ? 'Customer deleted successfully.' : session('status')))
                    ) : session('status') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="p-4 bg-red-500/10 text-red-400 rounded border border-red-500/20">
                    <ul class="list-disc {{ $isEnglish ? 'pl-5' : 'pr-5' }} space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Add customer --}}
            <div class="bg-surface border border-line rounded-lg p-6">
                <div class="flex items-center gap-2 mb-4">
                    <x-icon name="plus" class="w-5 h-5 text-neon" />
                    <h3 class="font-semibold text-ink">
                        {{ $labels['add_customer'] }}
                    </h3>
                </div>

                <form id="add-customer-form"
                      method="POST"
                      action="{{ route('customers.store') }}"
                      class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf

                    <div>
                        <label class="block text-xs text-muted mb-1">{{ $labels['name'] }}</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="{{ $labels['name'] }}"
                               required
                               class="w-full bg-surface2 border border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    </div>

                    <div>
                        <label class="block text-xs text-muted mb-1">{{ $labels['country'] }}</label>
                        <select name="country"
                                class="w-full bg-surface2 border border-line text-ink rounded focus:border-neon focus:ring-neon">
                            @foreach ($countries as $code => $countryLabel)
                                <option value="{{ $code }}" @selected(old('country', '970') === $code)>
                                    {{ $countryLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-muted mb-1">{{ $labels['phone'] }}</label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone') }}"
                               placeholder="{{ $labels['phone_placeholder'] }}"
                               dir="ltr"
                               class="w-full bg-surface2 border border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    </div>

                    <button type="submit"
                            class="bg-neon hover:bg-neon-dim text-night px-4 py-2.5 rounded font-semibold transition">
                        {{ $labels['add'] }}
                    </button>

                    <div class="md:col-span-4">
                        <label class="block text-xs text-muted mb-1">{{ $labels['notes'] }}</label>
                        <textarea name="notes"
                                  form="add-customer-form"
                                  rows="2"
                                  placeholder="{{ $labels['notes'] }}"
                                  class="w-full bg-surface2 border border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">{{ old('notes') }}</textarea>
                    </div>
                </form>
            </div>

            {{-- Customer list --}}
            <div class="bg-surface border border-line rounded-lg overflow-hidden">
                <div class="p-4 border-b border-line flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-ink">{{ $labels['customer_list'] }}</h3>
                        <p class="text-xs text-muted mt-1">
                            {{ $labels['total_customers'] }}:
                            <span class="text-neon font-semibold">
                                {{ number_format($customers->total(), 0, '.', ',') }}
                            </span>
                        </p>
                    </div>
                </div>

                @if ($customers->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm {{ $isEnglish ? 'text-left' : 'text-right' }}">
                            <thead class="bg-surface2">
                                <tr>
                                    <th class="p-3 text-muted font-medium">{{ $labels['name'] }}</th>
                                    <th class="p-3 text-muted font-medium">{{ $labels['phone'] }}</th>
                                    <th class="p-3 text-muted font-medium">{{ $labels['orders'] }}</th>
                                    <th class="p-3 text-muted font-medium">{{ $labels['notes'] }}</th>
                                    <th class="p-3 text-muted font-medium text-center">{{ $labels['actions'] }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($customers as $customer)
                                    @php
                                        [$country, $localPhone] = $getPhoneParts($customer->phone);
                                    @endphp

                                    <tr class="border-t border-line hover:bg-surface2/50 transition">
                                        <td class="p-3 text-ink font-medium">
                                            {{ $customer->name }}
                                        </td>

                                        <td class="p-3">
                                            @if ($customer->phone)
                                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $customer->phone) }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="text-neon hover:text-neon-dim inline-flex items-center gap-1"
                                                   dir="ltr">
                                                    <x-icon name="chat" class="w-4 h-4" />
                                                    {{ $customer->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td class="p-3 text-ink">
                                            {{ number_format((int) $customer->orders_count, 0, '.', ',') }}
                                        </td>

                                        <td class="p-3 text-muted max-w-xs">
                                            @if ($customer->notes)
                                                <span title="{{ $customer->notes }}">
                                                    {{ \Illuminate\Support\Str::limit($customer->notes, 40) }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td class="p-3">
                                            <div class="flex items-center justify-center gap-3">
                                                <button type="button"
                                                        onclick="document.getElementById('edit-customer-{{ $customer->id }}').classList.toggle('hidden')"
                                                        class="text-neon hover:text-neon-dim text-xs inline-flex items-center gap-1">
                                                    <x-icon name="pencil" />
                                                    {{ $labels['edit'] }}
                                                </button>

                                                <form method="POST"
                                                      action="{{ route('customers.destroy', $customer) }}"
                                                      onsubmit="return confirm(@js($labels['delete_confirm']))">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-red-400 hover:text-red-300 text-xs inline-flex items-center gap-1">
                                                        <x-icon name="trash" />
                                                        {{ $labels['delete'] }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Edit customer --}}
                                    <tr id="edit-customer-{{ $customer->id }}"
                                        class="hidden bg-surface2 border-t border-line">
                                        <td colspan="5" class="p-5">
                                            <form method="POST"
                                                  action="{{ route('customers.update', $customer) }}"
                                                  class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                                @csrf
                                                @method('PUT')

                                                <div>
                                                    <label class="block text-xs text-muted mb-1">{{ $labels['name'] }}</label>
                                                    <input type="text"
                                                           name="name"
                                                           value="{{ $customer->name }}"
                                                           required
                                                           class="w-full bg-surface border border-line text-ink rounded focus:border-neon focus:ring-neon">
                                                </div>

                                                <div>
                                                    <label class="block text-xs text-muted mb-1">{{ $labels['country'] }}</label>
                                                    <select name="country"
                                                            class="w-full bg-surface border border-line text-ink rounded focus:border-neon focus:ring-neon">
                                                        @foreach ($countries as $code => $countryLabel)
                                                            <option value="{{ $code }}" @selected($country === $code)>
                                                                {{ $countryLabel }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs text-muted mb-1">{{ $labels['phone'] }}</label>
                                                    <input type="text"
                                                           name="phone"
                                                           value="{{ $localPhone }}"
                                                           placeholder="{{ $labels['phone_placeholder'] }}"
                                                           dir="ltr"
                                                           class="w-full bg-surface border border-line text-ink rounded focus:border-neon focus:ring-neon">
                                                </div>

                                                <div class="flex gap-2">
                                                    <button type="submit"
                                                            class="bg-neon hover:bg-neon-dim text-night px-4 py-2.5 rounded font-semibold">
                                                        {{ $labels['save'] }}
                                                    </button>

                                                    <button type="button"
                                                            onclick="document.getElementById('edit-customer-{{ $customer->id }}').classList.add('hidden')"
                                                            class="bg-surface border border-line text-muted hover:text-ink px-4 py-2.5 rounded">
                                                        {{ $labels['cancel'] }}
                                                    </button>
                                                </div>

                                                <div class="md:col-span-4">
                                                    <label class="block text-xs text-muted mb-1">{{ $labels['notes'] }}</label>
                                                    <textarea name="notes"
                                                              rows="2"
                                                              class="w-full bg-surface border border-line text-ink rounded focus:border-neon focus:ring-neon">{{ $customer->notes }}</textarea>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-line">
                        {{ $customers->links() }}
                    </div>
                @else
                    <div class="p-10 text-center text-muted">
                        <x-icon name="folder" class="w-8 h-8 mx-auto mb-2" />
                        <h3 class="text-ink font-bold mb-1">{{ $labels['no_customers'] }}</h3>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
