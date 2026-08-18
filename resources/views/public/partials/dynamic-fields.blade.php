@foreach ($fields as $field)
    @php
        $key = $field['key'];
        $type = $field['type'] ?? 'text';
        $fieldPath = $path === '' ? $key : $path . '.' . $key;
        $value = data_get($content, $fieldPath);
    @endphp

    @if ($type === 'image')
        @if (! blank($value))
            @php
                $imageUrl = str_starts_with((string) $value, 'http')
                    ? $value
                    : Illuminate\Support\Facades\Storage::disk('public')->url($value);
            @endphp
            <img src="{{ $imageUrl }}" alt="{{ $field['label'] }}" class="w-full max-w-full h-auto rounded-lg my-3">
        @endif
    @elseif ($type === 'repeater')
        @if (! blank($value))
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ((array) $value as $item)
                    <div class="rounded-lg border border-gray-200 p-4">
                        @include('public.partials.dynamic-fields', [
                            'fields' => $field['fields'] ?? [],
                            'content' => (array) $item,
                            'path' => '',
                        ])
                    </div>
                @endforeach
            </div>
        @endif
    @elseif ($type === 'rich-text')
        @if (! blank($value))
            <div class="prose prose-slate max-w-none my-3">{!! $value !!}</div>
        @endif
    @elseif ($type === 'link')
        @php
            $linkData = is_array($value) ? $value : [];
            $linkLabel = $linkData['label'] ?? '';
            $linkUrl = '#';
            $linkNewTab = $linkData['open_in_new_tab'] ?? false;

            if (! blank($linkLabel)) {
                $linkType = $linkData['link_type'] ?? 'internal';
                $linkUrl = match ($linkType) {
                    'external', 'internal' => $linkData['url'] ?? '#',
                    'page' => function () use ($linkData, $locale) {
                        $pageId = $linkData['page_id'] ?? null;
                        if ($pageId === null) {
                            return '#';
                        }
                        $page = \App\Models\Page::with('translations')->find($pageId);
                        if ($page === null) {
                            return '#';
                        }
                        $translation = $page->translations->firstWhere('locale', $locale);
                        if ($translation === null) {
                            $translation = $page->translations->firstWhere('locale', config('cms.default_locale'));
                        }
                        return $translation ? '/' . $locale . '/' . $translation->slug : '#';
                    },
                    default => '#',
                };
                if (is_callable($linkUrl)) {
                    $linkUrl = $linkUrl();
                }
            }
        @endphp
        @if (! blank($linkLabel))
            <div class="my-3">
                <a href="{{ $linkUrl }}" {!! $linkNewTab ? 'target="_blank" rel="noopener"' : '' !!}
                    class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 font-medium underline decoration-indigo-300 underline-offset-2 transition-colors">
                    {{ $linkLabel }}
                    @if ($linkNewTab)
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    @endif
                </a>
            </div>
        @endif
    @else
        @if (! blank($value) && ! is_array($value))
            @if (str_contains($key, 'heading') || str_contains($key, 'title'))
                <h2 class="mt-6 mb-3 text-2xl font-bold text-gray-900">{{ $value }}</h2>
            @elseif (str_contains($key, 'subtitle') || str_contains($key, 'subheading'))
                <h3 class="mt-4 mb-2 text-xl font-semibold text-gray-800">{{ $value }}</h3>
            @elseif ($type === 'textarea')
                <p class="my-2 text-gray-700 whitespace-pre-line">{{ $value }}</p>
            @else
                <p class="my-2 text-gray-700">{{ $value }}</p>
            @endif
        @endif
    @endif
@endforeach
