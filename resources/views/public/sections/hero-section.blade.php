{{--
    Template Section: Hero Section (hero-section)
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
<section class="relative overflow-hidden rounded-3xl bg-slate-900 text-white p-8 sm:p-12 lg:p-16 border border-slate-800 shadow-2xl">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-5xl mx-auto space-y-6">
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
                    <div class="overflow-hidden rounded-2xl border border-white/10 shadow-2xl my-6">
                        <img src="{{ $imageUrl }}" alt="{{ $field['label'] }}" class="w-full h-auto max-h-[500px] object-cover hover:scale-105 transition-transform duration-500 ease-out">
                    </div>
                @endif
            @elseif ($type === 'repeater')
                @if (! blank($value))
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 pt-6">
                        @foreach ((array) $value as $item)
                            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 backdrop-blur-md transition-all duration-300 hover:-translate-y-1 hover:bg-white/10 hover:border-indigo-500/50 shadow-lg">
                                @foreach (($field['fields'] ?? []) as $subField)
                                    @php $subValue = data_get($item, $subField['key']); @endphp
                                    @if (! blank($subValue))
                                        @if (str_contains($subField['key'], 'heading') || str_contains($subField['key'], 'title'))
                                            <h3 class="text-lg font-bold text-white mb-2">{{ $subValue }}</h3>
                                        @elseif (($subField['type'] ?? 'text') === 'textarea')
                                            <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ $subValue }}</p>
                                        @else
                                            <p class="text-slate-300 text-sm leading-relaxed">{{ $subValue }}</p>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            @elseif ($type === 'rich-text')
                @if (! blank($value))
                    <div class="prose prose-invert prose-indigo max-w-none text-slate-300 my-4">{!! $value !!}</div>
                @endif
            @else
                @if (! blank($value))
                    @if (str_contains($key, 'heading') || str_contains($key, 'title'))
                        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-indigo-300 leading-tight">{{ $value }}</h2>
                    @elseif (str_contains($key, 'subtitle') || str_contains($key, 'subheading'))
                        <h3 class="text-lg sm:text-xl font-medium text-indigo-300 leading-relaxed max-w-3xl">{{ $value }}</h3>
                    @elseif ($type === 'textarea')
                        <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-3xl whitespace-pre-line">{{ $value }}</p>
                    @else
                        <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-3xl">{{ $value }}</p>
                    @endif
                @endif
            @endif
        @endforeach
    </div>
</section>