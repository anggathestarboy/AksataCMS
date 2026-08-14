@extends('layouts.public')

@section('content')
    @if ($page->sections->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-extrabold text-gray-900">{{ $translation->title }}</h1>

            <div class="mt-8 space-y-6">
                @foreach ($page->sections as $section)
                    @include('public.partials.section', ['section' => $section, 'locale' => $locale])
                @endforeach
            </div>
        </div>
    @endif
@endsection
