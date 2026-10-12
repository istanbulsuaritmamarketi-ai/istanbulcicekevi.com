<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ürün Yorumları & Değerlendirmeler') }}
            </h2>
            @if($pendingCount > 0)
                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-200 flex items-center gap-1.5">
                    <i class="fas fa-bell text-amber-600"></i> {{ $pendingCount }} Onay Bekleyen Yorum
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 text-green-700 rounded-r shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtre Sekmeleri -->
            <div class="mb-6 flex gap-2">
                <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 text-xs font-bold rounded-lg {{ !request('status') ? 'bg-blue-600 text-white shadow' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                    Tüm Yorumlar
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-4 py-2 text-xs font-bold rounded-lg {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                    Onay Bekleyenler ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="px-4 py-2 text-xs font-bold rounded-lg {{ request('status') === 'approved' ? 'bg-green-600 text-white shadow' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                    Onaylanmışlar
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ürün</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Puan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Yorum</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reviews as $rev)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($rev->product)
                                                <a href="{{ route('product.show', $rev->product->slug) }}" target="_blank" class="font-bold text-xs text-blue-600 hover:underline">
                                                    {{ $rev->product->name }}
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">Ürün Silinmiş</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-sm text-gray-900">{{ $rev->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $rev->email ?: '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex text-amber-400 text-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <span class="text-[11px] font-bold text-gray-600">{{ $rev->rating }}/5</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 max-w-xs">{{ $rev->comment }}</p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($rev->is_approved)
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Onaylandı
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 animate-pulse">
                                                    Onay Bekliyor
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $rev->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-2">
                                            <form action="{{ route('admin.reviews.toggle', $rev) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 rounded-md text-xs font-bold {{ $rev->is_approved ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                                    <i class="fas {{ $rev->is_approved ? 'fa-ban' : 'fa-check' }}"></i> {{ $rev->is_approved ? 'Onayı Kaldır' : 'Onayla' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reviews.destroy', $rev) }}" method="POST" class="inline" onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md text-xs font-bold">
                                                    <i class="fas fa-trash"></i> Sil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <i class="far fa-comments text-4xl text-gray-300 mb-3"></i>
                                            <p class="font-medium">Henüz ürün değerlendirmesi / yorumu yok.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
