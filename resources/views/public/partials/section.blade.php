@php
    $fields = $section->sectionType->fields ?? [];
    $content = $section->translations->firstWhere('locale', $locale)?->content ?? [];

    $hasContent = collect($content)->contains(fn (mixed $value): bool => ! blank($value));

    $templateView = 'public.sections.' . $section->sectionType->slug;
    $hasTemplate = \Illuminate\Support\Facades\View::exists($templateView);
@endphp

@if ($hasContent)
    @if ($hasTemplate)
        @include($templateView, [
            'sectionType' => $section->sectionType,
            'section' => $section,
            'locale' => $locale,
            'content' => $content,
            'fields' => $fields,
        ])
    @else
        <section class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
            @include('public.partials.dynamic-fields', [
                'fields' => $fields,
                'content' => $content,
                'path' => '',
            ])
        </section>
    @endif
@endif
