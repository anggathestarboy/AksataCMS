<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100">

            <!-- Mobile overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
                class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-slate-900 text-slate-300 transition-transform duration-200 ease-in-out lg:translate-x-0">

                <!-- Brand -->
                <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-800 px-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <x-application-mark class="block h-8 w-auto" />
                        <span class="text-sm font-semibold tracking-wide text-white">Admin Panel</span>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                    @php
                        $navItems = [
                            [
                                'label' => __('Dashboard'),
                                'route' => 'dashboard',
                                'active' => request()->routeIs('dashboard'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />',
                            ],
                            [
                                'label' => __('Pages'),
                                'route' => 'admin.pages.index',
                                'active' => request()->routeIs('admin.pages.*'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />',
                            ],
                            [
                                'label' => __('Section Types'),
                                'route' => 'admin.section-types.index',
                                'active' => request()->routeIs('admin.section-types.*'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />',
                            ],
                            [
                                'label' => __('Navigations'),
                                'route' => 'admin.navigations.index',
                                'active' => request()->routeIs('admin.navigations.*'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />',
                            ],
                            [
                                'label' => __('Media'),
                                'route' => 'admin.media.index',
                                'active' => request()->routeIs('admin.media.*'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z" />',
                            ],
                            [
                                'label' => __('Settings'),
                                'route' => 'admin.settings.index',
                                'active' => request()->routeIs('admin.settings.*'),
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                            ],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ $item['active'] ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $item['icon'] !!}</svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- User -->
                <div class="shrink-0 border-t border-slate-800 p-4">
                    <div class="flex items-center gap-3 px-2">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="size-9 rounded-full object-cover ring-2 ring-slate-700" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                        @endif
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                            <div class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main column -->
            <div class="min-w-0 flex-1 lg:pl-64">
                @php
                    $currentPageLabel = match (true) {
                        request()->routeIs('admin.pages.*') => __('Pages'),
                        request()->routeIs('admin.section-types.*') => __('Section Types'),
                        request()->routeIs('admin.navigations.*') => __('Navigations'),
                        request()->routeIs('admin.media.*') => __('Media'),
                        request()->routeIs('admin.settings.*') => __('Settings'),
                        request()->routeIs('profile.show') => __('Profile'),
                        request()->routeIs('api-tokens.*') => __('API Tokens'),
                        default => __('Dashboard'),
                    };
                @endphp

                <!-- Top navbar -->
                <nav class="flex h-16 items-center gap-3 border-b border-gray-200 bg-white px-4 sm:px-6">
                    <button @click="sidebarOpen = ! sidebarOpen" class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none lg:hidden" aria-label="Toggle navigation">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <span class="text-base font-semibold text-gray-900">{{ $currentPageLabel }}</span>

                    <div class="ms-auto flex items-center gap-3">
                        <!-- Profile dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center rounded-full p-1 text-gray-500 hover:text-gray-700 focus:outline-none focus:bg-gray-100 active:bg-gray-100 transition ease-in-out duration-150">
                                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                            <img class="size-9 rounded-full object-cover ring-1 ring-gray-200" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                        @else
                                            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                            <svg class="ms-1 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        @endif
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 text-xs text-gray-400">
                                    {{ Auth::user()->name }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-200"></div>

                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </nav>

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')

        @include('livewire.admin.partials.confirm-modal')
        @livewire('admin.media.picker')

        {{-- Global Toast Notification --}}
        <div x-data="{ show: false, message: '', type: 'success' }"
            x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            x-cloak
            class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-white"
            :class="type === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
            <svg x-show="type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <svg x-show="type !== 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span x-text="message"></span>
        </div>

        @livewireScripts
    </body>
</html>
