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
            <img src="@field('image', $item)" alt="">
            <h3>@field('title', $item)</h3>
            <p>@field('description', $item)</p>
        </div>
    @endforeach
</section>
