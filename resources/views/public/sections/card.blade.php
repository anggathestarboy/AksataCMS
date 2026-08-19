{{--
    Template Section: card (card)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section>
        @field('card-heading')
    @field('card-desc')
    @repeater('list-card')
        @field('heading-card')
        @field('desc-card')
        @field('image-card')
        <a href="@field('link-card')" target="@field('link-card_target')">@field('link-card_label')</a>
    @endrepeater
</section>