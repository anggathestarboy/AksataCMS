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

<section class="relative left-1/2 w-screen -translate-x-1/2 min-h-[500px] lg:min-h-[600px] flex items-center justify-center bg-cover bg-center bg-no-repeat overflow-hidden rounded-xl shadow-lg {{ $bgUrl ? '' : 'bg-gray-900' }}"
         @if($bgUrl) style="background-image: url('{{ $bgUrl }}');" @endif>
    
    {{-- Dark Overlay untuk keterbacaan teks --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px]"></div>

    {{-- Hero Content Container --}}
    <div class="relative z-10 container mx-auto px-6 py-16 text-center max-w-4xl">
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
                                @foreach (($field['fields'] ?? []) as $subField)
                                    @php $subValue = data_get($item, $subField['key']); @endphp
                                    @if (! blank($subValue))
                                        @if (str_contains($subField['key'], 'heading') || str_contains($subField['key'], 'title'))
                                            <h3 class="mt-1 mb-1 text-lg font-semibold text-white">{{ $subValue }}</h3>
                                        @elseif (($subField['type'] ?? 'text') === 'textarea')
                                            <p class="text-gray-200 text-sm whitespace-pre-line">{{ $subValue }}</p>
                                        @else
                                            <p class="text-gray-200 text-sm">{{ $subValue }}</p>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif

            @elseif ($type === 'rich-text')
                @if (! blank($value))
                    <div class="prose prose-invert max-w-none my-4 text-gray-200 leading-relaxed">
                        {!! $value !!}
                    </div>
                @endif

            @else
                @if (! blank($value))
                    @if (str_contains($key, 'heading') || str_contains($key, 'title'))
                        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight my-3">
                            {{ $value }}
                        </h1>
                    @elseif (str_contains($key, 'subtitle') || str_contains($key, 'subheading'))
                        <h2 class="text-lg sm:text-2xl font-medium text-gray-200 my-3">
                            {{ $value }}
                        </h2>
                    @elseif ($type === 'textarea')
                        <p class="my-3 text-base sm:text-lg text-gray-300 whitespace-pre-line leading-relaxed max-w-2xl mx-auto">
                            {{ $value }}
                        </p>
                    @else
                        <p class="my-2 text-base text-gray-300">
                            {{ $value }}
                        </p>
                    @endif
                @endif
            @endif
        @endforeach
    </div>
</section>