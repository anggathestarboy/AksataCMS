{{--
    Template Section: Features (features)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section>
        @repeater('items')
        @field('title')
    @endrepeater
</section>