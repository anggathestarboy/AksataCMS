@extends('layouts.public')

@section('content')
    @if ($page->sections->isNotEmpty())
        @foreach ($page->sections as $section)
            @include('public.partials.section', ['section' => $section, 'locale' => $locale])
        @endforeach
    @endif
@endsection