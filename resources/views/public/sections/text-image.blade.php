{{--
    Template Section: Text + image (text-image)
    =========================================================
    Gambar di kiri, teks di kanan (desktop).
    Di mobile, gambar tampil di atas, teks di bawah.
    =========================================================
--}}
@php
    $imageField = collect($fields)->firstWhere('type', 'image');
    $imageRaw = $imageField ? data_get($content, $imageField['key']) : null;

    $imageUrl = null;
    if (! blank($imageRaw)) {
        $imageUrl = str_starts_with((string) $imageRaw, 'http')
            ? $imageRaw
            : \Illuminate\Support\Facades\Storage::disk('public')->url($imageRaw);
    }
@endphp

<section class="w-full bg-white py-12 sm:py-16 lg:py-20">

    {{-- INNER CONTAINER: Pembatas isi di tengah --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 items-center">

            {{-- KOLOM KIRI: Gambar --}}
            <div class="order-1 flex justify-center items-center">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}"
                         alt="{{ $imageField['label'] ?? 'Image' }}"
                         class="w-full h-auto rounded-xl shadow-lg">
                @endif
            </div>

            {{-- KOLOM KANAN: Teks --}}
            <div class="order-2 space-y-5">
                @foreach ($fields as $field)
                    @php
                        $key = $field['key'];
                        $type = $field['type'] ?? 'text';
                        $value = data_get($content, $key);
                    @endphp

                    {{-- Skip gambar karena dirender di kolom kiri --}}
                    @if ($type === 'image' || ($imageField && $key === $imageField['key']))
                        @continue
                    @endif

                    @if ($type === 'repeater')
                        @if (! blank($value))
                            <div class="grid gap-4 sm:grid-cols-2 mt-4">
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
                        @if (! blank($value) && ! is_array($value))
                            @if (str_contains($key, 'heading') || str_contains($key, 'title'))
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">{{ $value }}</h2>
                            @elseif (str_contains($key, 'subtitle') || str_contains($key, 'subheading'))
                                <h3 class="text-lg sm:text-xl font-semibold text-gray-800">{{ $value }}</h3>
                            @elseif ($type === 'textarea')
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $value }}</p>
                            @else
                                <p class="text-gray-700 leading-relaxed">{{ $value }}</p>
                            @endif
                        @endif
                    @endif
                @endforeach
            </div>

        </div>
    </div>
</section>
