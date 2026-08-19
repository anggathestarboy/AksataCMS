{{--
    Template Section: Text + image
    =========================================================
    Gambar di kiri, teks di kanan (desktop).

    Ganti 'image', 'heading', 'description' dengan key field kamu.
    =========================================================
--}}
<section>
    <div>
        <img src="@field('image')" alt="">
    </div>
    <div>
        @field('title')
        @field('description')
    </div>
</section>
