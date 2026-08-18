{{--
    Template Section: Dropdown (acordion)
    =========================================================
    Satu tombol dropdown bergaya accordion:
    klik heading (trigger) untuk membuka/menutup panel konten.
    =========================================================
--}}
@php
    $headingField = collect($fields)->first(fn ($f) =>
        str_contains($f['key'] ?? '', 'heading') || str_contains($f['key'] ?? '', 'title'));
    $heading = $headingField ? data_get($content, $headingField['key']) : null;
    $heading = blank($heading) ? 'Dropdown Title' : $heading;
@endphp

<section class="py-12 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div x-data="{ open: false }" class="mt-8">
            {{-- Dropdown Trigger (mirip header accordion) --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden transition-all duration-300 shadow-sm"
                 :class="open ? 'border-indigo-500 ring-2 ring-indigo-500/10 shadow-md bg-white' : 'bg-gray-50/60 hover:bg-white hover:border-gray-300'">

                <button type="button"
                        @click="open = !open"
                        :aria-expanded="open ? 'true' : 'false'"
                        class="w-full flex items-center justify-between p-5 text-left font-semibold text-gray-900 focus:outline-none select-none">

                    <span class="text-base sm:text-lg pr-4 transition-colors"
                          :class="open ? 'text-indigo-600 font-bold' : 'text-gray-800'">
                        {{ $heading }}
                    </span>

                    {{-- Icon Indicator (Rotates smoothly) --}}
                    <div class="shrink-0 text-gray-400 transition-transform duration-300"
                         :class="open ? 'rotate-180 text-indigo-600' : ''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>

                {{-- Panel Konten (Smooth Animated Dropdown) --}}
                <div x-show="open"
                     x-collapse
                     x-cloak
                     class="px-5 pb-5 pt-1 text-gray-600 border-t border-gray-100">

                    @foreach ($fields as $field)
                        @php
                            $key = $field['key'];
                            $type = $field['type'] ?? 'text';
                            $value = data_get($content, $key);
                        @endphp

                        {{-- Skip heading karena sudah jadi tombol trigger --}}
                        @if ($headingField && $key === $headingField['key'])
                            @continue
                        @endif

                        @if ($type === 'image')
                            @if (! blank($value))
                                @php
                                    $imageUrl = str_starts_with((string) $value, 'http')
                                        ? $value
                                        : \Illuminate\Support\Facades\Storage::disk('public')->url($value);
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $field['label'] ?? 'Image' }}" class="w-full h-auto rounded-xl shadow-md my-4">
                            @endif

                        @elseif ($type === 'repeater')
                            @if (! blank($value))
                                <div class="my-3 space-y-2">
                                    @foreach ((array) $value as $item)
                                        @include('public.partials.dynamic-fields', [
                                            'fields' => $field['fields'] ?? [],
                                            'content' => (array) $item,
                                            'path' => '',
                                        ])
                                    @endforeach
                                </div>
                            @endif

                        @elseif ($type === 'rich-text')
                            @if (! blank($value))
                                <div class="prose prose-slate max-w-none text-sm sm:text-base leading-relaxed my-3">{!! $value !!}</div>
                            @endif

                        @else
                            @if (! blank($value) && ! is_array($value))
                                <p class="whitespace-pre-line leading-relaxed text-sm sm:text-base text-gray-600 my-3">{{ $value }}</p>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
