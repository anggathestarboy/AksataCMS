{{--
    Template Section: Test Section (test-section)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section>
        @field('test-heading')
    @field('test-desc')
    @field('test-image')
    @foreach (($content['test-loop'] ?? []) as $item)
        @field('loop-heading', $item)
        @field('loop-desc', $item)
    @endforeach
    @field('test-link')
</section>