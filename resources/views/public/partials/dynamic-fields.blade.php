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
    @else
        @if (! blank($value))
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
