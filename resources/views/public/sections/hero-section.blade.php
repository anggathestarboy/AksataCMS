{{--
    Template Section: Hero homepage (hero-homepage)
    =========================================================
--}}
@php
    // 1. Cari field bertipe 'image' atau key yang mengandung kata 'image'/'bg'/'background'
    $imageField = collect($fields)->first(function ($f) {
        return ($f['type'] ?? '') === 'image' || str_contains($f['key'] ?? '', 'image') || str_contains($f['key'] ?? '', 'bg');
    });

    $bgUrl = null;
    if ($imageField) {
        $rawBg = data_get($content, $imageField['key']);
        if (! blank($rawBg)) {
            $bgUrl = str_starts_with((string) $rawBg, 'http')
                ? $rawBg
                : \Illuminate\Support\Facades\Storage::disk('public')->url($rawBg);
        }
    }
@endphp

<section class="relative w-full bg-cover bg-center bg-no-repeat overflow-hidden {{ $bgUrl ? '' : 'bg-gray-900' }}"
         @if($bgUrl) style="background-image: url('{{ $bgUrl }}');" @endif>

    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-36 text-left">
        @foreach ($fields as $field)
            @php
                $key = $field['key'];
                $type = $field['type'] ?? 'text';
                $value = data_get($content, $key);
            @endphp

            {{-- Lewati rendering gambar secara terpisah karena sudah dipakai sebagai Background --}}
            @if ($type === 'image' || (isset($imageField['key']) && $key === $imageField['key']))
                @continue
            @endif

            @if ($type === 'repeater')
                @if (! blank($value))
                    <div class="mt-8 grid gap-4 sm:grid-cols-2 text-left">
                        @foreach ((array) $value as $item)
                            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-5 text-white shadow-sm">
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
                    <div class="prose prose-invert max-w-none my-4 text-gray-100 leading-relaxed">
                        {!! $value !!}
                    </div>
                @endif

            @else
                @if (! blank($value) && ! is_array($value))
                    @if (str_contains($key, 'heading') || str_contains($key, 'title'))
                        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight my-3">
                            {{ $value }}
                        </h1>
                    @elseif (str_contains($key, 'subtitle') || str_contains($key, 'subheading'))
                        <h2 class="text-lg sm:text-2xl font-medium text-gray-100 my-3">
                            {{ $value }}
                        </h2>
                    @elseif ($type === 'textarea')
                        {{-- Dihapus mx-auto agar rata kiri penuh --}}
                        <p class="my-3 text-base sm:text-lg text-gray-100 whitespace-pre-line leading-relaxed max-w-2xl">
                            {{ $value }}
                        </p>
                    @else
                        <p class="my-2 text-base text-gray-100">
                            {{ $value }}
                        </p>
                    @endif
                @endif
            @endif
        @endforeach
    </div>
</section>