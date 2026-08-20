{{--
    Template Section: Hero (hero)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section>
        @field('heading')
    @field('body')
    @field('content')
    <img
        src="@field('photo')"
        width="@field('photo_width')"
        height="@field('photo_height')"
        alt="@field('photo_alt')"
        loading="@field('photo_loading')"
        decoding="async"
    >
    @repeater('items')
        @field('title')
    @endrepeater
</section>