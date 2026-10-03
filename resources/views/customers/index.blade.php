<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="users" class="w-5 h-5 text-neon" /> العملاء
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-3 bg-neon/10 text-neon rounded border border-neon/20">{{ session('status') }}</div>
            @endif

            <div class="bg-surface border border-line rounded p-6">
                <h3 class="font-semibold mb-3 text-ink">إضافة عميل جديد</h3>
                <form method="POST" action="{{ route('customers.store') }}" class="grid grid-cols-3 gap-3 items-end">
                    @csrf
                    <input type="text" name="name" placeholder="الاسم" required class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    <input type="text" name="phone" placeholder="رقم واتساب" class="bg-surface2 border-line text-ink placeholder-muted rounded focus:border-neon focus:ring-neon">
                    <button class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold">إضافة</button>
                </form>
            </div>

            <div class="bg-surface border border-line rounded overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-surface2">
                        <tr>
                            <th class="p-3 text-muted font-medium">الاسم</th>
                            <th class="p-3 text-muted font-medium">الجوال</th>
                            <th class="p-3 text-muted font-medium">عدد الطلبات</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr class="border-t border-line">
                                <td class="p-3 text-ink">{{ $customer->name }}</td>
                                <td class="p-3">
                                    @if ($customer->phone)
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $customer->phone) }}" target="_blank" rel="noopener"
                                        class="text-neon hover:text-neon-dim inline-flex items-center gap-1">
                                            <x-icon name="chat" class="w-4 h-4" /> {{ $customer->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-ink">{{ $customer->orders_count }}</td>
                                <td class="p-3">
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('حذف العميل؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-400 hover:text-red-300 text-xs flex items-center gap-1">
                                            <x-icon name="trash" /> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-muted">
                                    <x-icon name="folder" class="w-8 h-8 mx-auto mb-2" />
                                    لا يوجد عملاء بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $customers->links() }}</div>
        </div>
    </div>
</x-app-layout>
