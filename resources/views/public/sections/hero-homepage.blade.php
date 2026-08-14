{{--
    Template Section: Hero homepage (hero-homepage)
    =========================================================
    File ini digenerate otomatis saat Section Type dibuat.
    Edit bebas untuk menyesuaikan tampilan section di halaman publik.

    Variabel yang tersedia:
        $sectionType  => App\Models\SectionType
        $section      => App\Models\Section
        $locale       => string, kode locale aktif (mis. "id" atau "en")
        $content      => array, isi konten untuk locale aktif
        $fields       => array, definisi field dari Section Type

    Contoh akses nilai field:
        {{ $content['heading'] ?? '' }}
    =========================================================
--}}
<section class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
    @foreach ($fields as $field)
        @php
            $key = $field['key'];
            $type = $field['type'] ?? 'text';
            $value = data_get($content, $key);
        @endphp

        @if ($type === 'image')
            @if (! blank($value))
                @php
                    $imageUrl = str_starts_with((string) $value, 'http')
                        ? $value
                        : \Illuminate\Support\Facades\Storage::disk('public')->url($value);
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $field['label'] }}" class="w-full max-w-full h-auto rounded-lg my-3">
            @endif
        @elseif ($type === 'repeater')
            @if (! blank($value))
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ((array) $value as $item)
                        <div class="rounded-lg border border-gray-200 p-4">
                            @foreach (($field['fields'] ?? []) as $subField)
                                @php $subValue = data_get($item, $subField['key']); @endphp
                                @if (! blank($subValue))
                                    @if (str_contains($subField['key'], 'heading') || str_contains($subField['key'], 'title'))
                                        <h3 class="mt-2 mb-1 text-lg font-semibold text-gray-800">{{ $subValue }}</h3>
                                    @elseif (($subField['type'] ?? 'text') === 'textarea')
                                        <p class="text-gray-700 whitespace-pre-line">{{ $subValue }}</p>
                                    @else
                                        <p class="text-gray-700">{{ $subValue }}</p>
                                    @endif
                                @endif
                            @endforeach
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
</section>