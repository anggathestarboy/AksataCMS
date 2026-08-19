{{--
    Template Section: Hero homepage
    =========================================================
    Gambar sebagai background, konten di atasnya.

    Ganti 'bg_image', 'heading', 'subtitle', 'content', 'features'
    dengan key field kamu.
    =========================================================
--}}
@php
    $bgRaw = $content['bg_image'] ?? null;
    $bgUrl = blank($bgRaw) ? null : (
        str_starts_with((string) $bgRaw, 'http')
            ? $bgRaw
            : \Illuminate\Support\Facades\Storage::disk('public')->url($bgRaw)
    );
@endphp

<section style="background-image: url('{{ $bgUrl }}');">
    <h1>@field('heading')</h1>
    <p>@field('subtitle')</p>
    @field('content')

    @foreach (($content['features'] ?? []) as $item)
        <div>
            <h3>@field('title', $item)</h3>
            <p>@field('description', $item)</p>
        </div>
    @endforeach
</section>
