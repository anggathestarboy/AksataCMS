{{--
    Template Section: Overview
    =========================================================
    Grid items dengan repeater.

    Ganti 'heading', 'items', 'title', 'description', 'image'
    dengan key field kamu.
    =========================================================
--}}
<section>
    @field('heading')

    @foreach (($content['items'] ?? []) as $item)
        <div>
            <img
                src="@field('image', $item)"
                width="@field('image_width', $item)"
                height="@field('image_height', $item)"
                alt="@field('image_alt', $item)"
                loading="@field('image_loading', $item)"
                decoding="async"
            >
            <h3>@field('title', $item)</h3>
            <p>@field('description', $item)</p>
        </div>
    @endforeach
</section>
