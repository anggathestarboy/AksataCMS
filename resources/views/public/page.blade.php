@extends('layouts.public')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-extrabold text-gray-900">{{ $translation->title }}</h1>

        @if ($page->sections->isEmpty())
            <p class="mt-8 text-gray-500">This page has no sections yet.</p>
        @endif

        <div class="mt-8 space-y-6">
            @foreach ($page->sections as $section)
                @include('public.partials.section', ['section' => $section, 'locale' => $locale])
            @endforeach
        </div>
    </div>
@endsection
