{{--
    Template Section: Card
    =========================================================
    Grid cards dengan repeater.

    Ganti 'heading', 'cards', 'title', 'description', 'image'
    dengan key field kamu.
    =========================================================
--}}
<section>
    @field('card-heading')

    @foreach (($content['list-card'] ?? []) as $item)
        <div>
            <img src="@field('image-card', $item)" alt="">
            <h3>@field('heading-card', $item)</h3>
            <p>@field('desc-card', $item)</p>
        </div>
    @endforeach
</section>
