<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sayfalar</h2>
            <a href="{{ route('admin.pages.create') }}" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">Yeni Sayfa Ekle</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                @endif

                <p class="text-sm text-gray-500 mb-4">
                    Buraya eklediğiniz sayfalar, sitenin footer (alt) kısmında <strong>"Kurumsal ve Hukuki"</strong> bölümünde otomatik olarak görünür ve
                    <code class="bg-gray-100 px-1 rounded">/sayfa/sayfa-slug</code> adresinde yayına girer.
                </p>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sayfa Başlığı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                            <th class="px-6 py-3 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pages as $item)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $item->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <a href="{{ route('page.show', $item->slug) }}" target="_blank" class="text-blue-500 hover:underline">
                                        /sayfa/{{ $item->slug }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Gizli' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.pages.edit', $item) }}" class="text-blue-600 hover:text-blue-900 mr-4">Düzenle</a>
                                    <form action="{{ route('admin.pages.destroy', $item) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bu sayfayı silmek istediğinize emin misiniz?')">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Henüz sayfa eklenmemiş.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>