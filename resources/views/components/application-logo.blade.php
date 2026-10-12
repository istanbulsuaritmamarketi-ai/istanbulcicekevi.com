@if(!empty($siteSettings['logo']))
    <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="{{ $siteSettings['site_title'] ?? 'İstanbul Çiçek Evi' }}" {{ $attributes->merge(['class' => 'h-14 w-auto object-contain']) }}>
@else
    <img src="{{ asset('images/logo.svg') }}?v={{ @filemtime(public_path('images/logo.svg')) ?: 20261009 }}" alt="{{ config('app.name', 'İstanbul Çiçek Evi') }}" {{ $attributes->merge(['class' => 'h-12 sm:h-14 w-auto object-contain']) }}>
@endif
