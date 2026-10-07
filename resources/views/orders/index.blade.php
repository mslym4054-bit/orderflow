<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight flex items-center gap-2">
            <x-icon name="grid" class="w-5 h-5 text-neon" /> الطلبات
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 bg-neon/10 text-neon rounded border border-neon/20 flex items-center gap-2">
                    <x-icon name="check-circle" /> {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                <div class="bg-surface border border-line rounded p-4">
                    <div class="text-sm text-muted">مبيعات هذا الشهر</div>
                    <div class="text-2xl font-bold text-ink mt-1">{{ number_format($stats['revenue'], 2) }}</div>
                </div>
                <div class="bg-surface border border-line rounded p-4">
                    <div class="text-sm text-muted">التكاليف</div>
                    <div class="text-2xl font-bold text-ink mt-1">{{ number_format($stats['cost'], 2) }}</div>
                </div>
                <div class="bg-surface border border-line rounded p-4">
                    <div class="text-sm text-muted">صافي الربح</div>
                    <div class="text-2xl font-bold mt-1 {{ $stats['profit'] >= 0 ? 'text-neon' : 'text-red-400' }}">{{ number_format($stats['profit'], 2) }}</div>
                </div>
            </div>



            <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('orders.index') }}"
                       class="px-3 py-1 rounded text-sm {{ !$currentStatus ? 'bg-neon text-night font-medium' : 'bg-surface2 border border-line text-muted hover:text-ink' }}">الكل</a>
                    @foreach ($statuses as $status)
                        <a href="{{ route('orders.index', ['status' => $status]) }}"
                           class="px-3 py-1 rounded text-sm {{ $currentStatus === $status ? 'bg-neon text-night font-medium' : 'bg-surface2 border border-line text-muted hover:text-ink' }}">{{ $status }}</a>
                    @endforeach
                </div>
                <a href="{{ route('orders.create') }}" class="bg-neon hover:bg-neon-dim text-night px-4 py-2 rounded font-semibold flex items-center gap-2">
                    <x-icon name="plus" /> طلب جديد
                </a>
            </div>

            <div class="bg-surface border border-line rounded overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-surface2">
                        <tr>
                            <th class="p-3 text-muted font-medium">العميل</th>
                            <th class="p-3 text-muted font-medium">المنتج</th>
                            <th class="p-3 text-muted font-medium">الكمية</th>
                            <th class="p-3 text-muted font-medium">سعر المنتج</th>
                            <th class="p-3 text-muted font-medium">سعر البيع</th>
                            <th class="p-3 text-muted font-medium">صافي الربح</th>
                            <th class="p-3 text-muted font-medium">الحالة</th>
                            <th class="p-3 text-muted font-medium">تاريخ</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $badge = match ($order->status) {
                                    'جديد' => 'bg-white/5 text-muted',
                                    'قيد التجهيز' => 'bg-amber-400/10 text-amber-300',
                                    'تم الشحن' => 'bg-neon/10 text-neon',
                                    'تم التسليم' => 'bg-emerald-400/10 text-emerald-300',
                                    'ملغي' => 'bg-red-400/10 text-red-300',
                                    default => 'bg-white/5 text-muted',
                                };
                               $item = $order->items->first();

$quantity = $item?->quantity ?? $order->quantity;
$cost = $item?->cost ?? $order->cost;
$price = $item?->price ?? $order->price;

$profit = ($price - $cost) * $quantity;
                            @endphp
                            <tr class="border-t border-line">
                                <td class="p-3 text-ink">
                                    {{ $order->customer->name }}
                                    @if ($order->customer->phone)
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $order->customer->phone) }}" target="_blank" rel="noopener"
                                           class="text-xs text-neon hover:text-neon-dim flex items-center gap-1">
                                            <x-icon name="chat" class="w-3 h-3" /> {{ $order->customer->phone }}
                                        </a>
                                    @endif
                                </td>
                                <td class="p-3 text-ink">{{ $order->items->first()?->item ?? $order->item }}</td>
                                <td class="p-3 text-ink">{{ $quantity }}</td>
                                <td class="p-3 text-ink">{{ $cost !== null ? number_format($cost, 2) : '-' }}</td>
                                <td class="p-3 text-ink">{{ $price !== null ? number_format($price, 2) : '-' }}</td>
                                <td class="p-3 {{ $profit >= 0 ? 'text-neon' : 'text-red-400' }}">
                                    {{ $price !== null ? number_format($profit, 2) : '-' }}
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $badge }}">{{ $order->status }}</span>
                                </td>
                                <td class="p-3 text-muted">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="p-3">
                                    <a href="{{ route('orders.edit', $order) }}" class="text-neon hover:text-neon-dim font-medium inline-flex items-center gap-1">
                                        <x-icon name="pencil" /> تعديل
                                    </a>
                                    <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="text-muted hover:text-ink font-medium inline-flex items-center gap-1 mr-2">
    <x-icon name="folder" class="w-4 h-4" /> فاتورة
</a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-muted">
                                    <x-icon name="folder" class="w-8 h-8 mx-auto mb-2" />
                                    لا يوجد طلبات بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>

</x-app-layout>
