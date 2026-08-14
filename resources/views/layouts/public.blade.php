<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteTitle = App\Models\Setting::get('site_title', config('app.name', 'ContentBlock'));
        $siteFooter = (string) App\Models\Setting::get('site_footer', '');
        $defaultSeoImage = (string) App\Models\Setting::get('default_seo_image', '');

        $meta = $translation->meta ?? [];
        $title = trim((string) ($meta['meta_title'] ?? '')) !== '' ? $meta['meta_title'] : $translation->title;
        $description = (string) ($meta['meta_description'] ?? '');

        $ogImage = (string) ($meta['og_image'] ?? '');
        if ($ogImage !== '' && !str_starts_with($ogImage, 'http')) {
            $ogImage = Illuminate\Support\Facades\Storage::disk('public')->url($ogImage);
        }
        if ($ogImage === '' && $defaultSeoImage !== '') {
            $ogImage = Illuminate\Support\Facades\Storage::disk('public')->url($defaultSeoImage);
        }
    @endphp

    <title>{{ $title }}</title>

    <meta name="description" content="{{ $description }}">

    @if ($ogImage !== '')
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CDN Fallback (Opsional untuk memastikan class Tailwind SELALU jalan) -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Alpine.js untuk interaksi publik (dropdown, accordion, dll.) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.9/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 overflow-x-hidden">
    <div class="min-h-screen flex flex-col">

        {{-- HEADER --}}
        <header class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-gray-900">
                    {{ $siteTitle }}
                </a>

                <nav class="flex items-center gap-6 text-sm font-medium">
                    @if ($page->translationFor(config('cms.default_locale')) !== null)
                        <a href="{{ url('/') }}"
                            class="transition-colors hover:text-indigo-600 {{ $locale === config('cms.default_locale') ? 'font-semibold text-indigo-600' : 'text-gray-600' }}">
                            Home
                        </a>
                    @endif

                    @foreach (config('cms.locales') as $localeKey => $label)
                        @php $target = $page->translationFor($localeKey); @endphp
                        @if ($target !== null)
                            <a href="/{{ $localeKey }}/{{ $target->slug }}"
                                class="transition-colors {{ $localeKey === $locale ? 'font-semibold text-indigo-600' : 'text-gray-500 hover:text-gray-900' }}">
                                {{ $label }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 w-full p-0 m-0 overflow-hidden">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        @if ($siteFooter !== '')
            <footer class="bg-white border-t border-gray-200 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-500 text-center">
                    {!! $siteFooter !!}
                </div>
            </footer>
        @endif
    </div>
</body>

</html>
