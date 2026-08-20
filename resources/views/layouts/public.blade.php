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
        $favicon = (string) App\Models\Setting::get('favicon', '');

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

    @if ($favicon !== '')
        <link rel="icon" type="image/x-icon" href="{{ Illuminate\Support\Facades\Storage::disk('public')->url($favicon) }}">
    @endif

    <meta name="description" content="{{ $description }}">

    @if ($ogImage !== '')
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

 

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
        <header class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50" x-data="{ mobileOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-gray-900">
                    {{ $siteTitle }}
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden lg:flex items-center gap-6 text-sm font-medium">
                    <x-navigation :slug="'navbar'" :locale="$locale" class="flex items-center gap-6" />

                    <div class="flex items-center rounded border border-gray-300 overflow-hidden text-xs font-semibold">
                        @foreach (config('cms.locales') as $localeKey => $label)
                            @php $target = $page->translationFor($localeKey); @endphp
                            @if ($target !== null)
                                <a href="/{{ $localeKey }}/{{ $target->slug }}"
                                    class="px-3 py-1 transition-colors {{ $localeKey === $locale ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                                    {{ strtoupper($localeKey) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </nav>

                {{-- Mobile Hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none" :aria-expanded="mobileOpen">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileOpen" x-collapse x-cloak class="lg:hidden border-t border-gray-200 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 space-y-3">
                    <x-navigation :slug="'navbar'" :locale="$locale" class="flex flex-col items-start gap-3" />

                    <div class="flex items-center rounded border border-gray-300 overflow-hidden text-xs font-semibold w-fit">
                        @foreach (config('cms.locales') as $localeKey => $label)
                            @php $target = $page->translationFor($localeKey); @endphp
                            @if ($target !== null)
                                <a href="/{{ $localeKey }}/{{ $target->slug }}"
                                    class="px-3 py-1 transition-colors {{ $localeKey === $locale ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                                    {{ strtoupper($localeKey) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 w-full p-0 m-0 overflow-hidden">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        @if ($siteFooter !== '')
            <footer class="bg-gray-500 border-t border-gray-200 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-white text-center">
                    {!! $siteFooter !!}
                </div>
            </footer>
        @endif
    </div>
</body>

</html>
