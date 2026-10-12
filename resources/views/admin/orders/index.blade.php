<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <i class="fas fa-shopping-bag text-rose-600"></i> {{ __('Siparişler') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Gelen müşteri siparişleri, ödeme ve teslimat durumları</p>
            </div>
            <a href="{{ route('admin.orders.export') }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-4 py-2.5 rounded-2xl shadow-sm transition-all flex items-center gap-2">
                <i class="fas fa-file-excel"></i> Siparişleri Excel'e Aktar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <a href="{{ route('admin.orders.index') }}" class="{{ (!isset($tab) || $tab == 'all') ? 'border-rose-500 text-rose-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Tüm Siparişler
                            </a>
                            <a href="{{ route('admin.orders.index', ['tab' => 'today']) }}" class="{{ (isset($tab) && $tab == 'today') ? 'border-rose-500 text-rose-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <i class="fas fa-calendar-day mr-1"></i> Bugünkü Siparişler
                            </a>
                            <a href="{{ route('admin.orders.index', ['tab' => 'active']) }}" class="{{ (isset($tab) && $tab == 'active') ? 'border-rose-500 text-rose-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Devam Edenler (Aktif)
                            </a>
                            <a href="{{ route('admin.orders.index', ['tab' => 'completed']) }}" class="{{ (isset($tab) && $tab == 'completed') ? 'border-rose-500 text-rose-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Tamamlananlar
                            </a>
                            <a href="{{ route('admin.orders.index', ['tab' => 'cancelled']) }}" class="{{ (isset($tab) && $tab == 'cancelled') ? 'border-rose-500 text-rose-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                İptaller
                            </a>
                        </nav>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sipariş No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bölge</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-rose-600"><a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">{{ $order->order_number }}</a></td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->region->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ number_format($order->total_amount, 2) }} TL</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColor = match($order->status) {
                                                    'Yeni' => 'bg-yellow-100 text-yellow-800',
                                                    'Onay Bekliyor' => 'bg-orange-100 text-orange-800',
                                                    'Hazırlanıyor' => 'bg-blue-100 text-blue-800',
                                                    'Yolda' => 'bg-purple-100 text-purple-800',
                                                    'Tamamlandı' => 'bg-green-100 text-green-800',
                                                    'İptal Edildi' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 px-3 py-1 rounded font-bold transition-colors">Detay</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Henüz hiç sipariş yok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
