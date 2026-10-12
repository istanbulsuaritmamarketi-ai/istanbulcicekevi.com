<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Yeni Banner Ekle</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Görsel *</label>
                        <input type="file" name="image" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                        <input type="text" name="title" class="w-full border-gray-300 rounded shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alt Başlık</label>
                        <input type="text" name="subtitle" class="w-full border-gray-300 rounded shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link URL (Tıklanınca Gideceği Adres)</label>
                        <input type="text" name="link_url" class="w-full border-gray-300 rounded shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sıra No</label>
                        <input type="number" name="order" value="0" class="w-full border-gray-300 rounded shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                            <span class="ml-2">Aktif (Sitede Görünsün)</span>
                        </label>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
