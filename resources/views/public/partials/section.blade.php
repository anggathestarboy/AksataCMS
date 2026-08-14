@php
    $fields = $section->sectionType->fields ?? [];
    $translation = $section->translations->firstWhere('locale', $locale);
    $content = $translation?->content ?? [];

    $hasContent = collect($content)->contains(fn (mixed $value): bool => ! blank($value));

    if (! $hasContent && $locale !== config('cms.default_locale')) {
        $defaultTranslation = $section->translations->firstWhere('locale', config('cms.default_locale'));
        $defaultContent = $defaultTranslation?->content ?? [];
        if (collect($defaultContent)->contains(fn (mixed $value): bool => ! blank($value))) {
            $content = $defaultContent;
            $hasContent = true;
        }
    }

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
