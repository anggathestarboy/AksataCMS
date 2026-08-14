@php
    $fields = $section->sectionType->fields ?? [];
    $content = $section->translations->firstWhere('locale', $locale)?->content ?? [];
@endphp

<section class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
    @include('public.partials.dynamic-fields', [
        'fields' => $fields,
        'content' => $content,
        'path' => '',
    ])
</section>
