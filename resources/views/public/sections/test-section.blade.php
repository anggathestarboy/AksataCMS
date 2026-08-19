{{--
    Template Section: Test Section (test-section)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section>
        @field('test-heading')<br>
    @field('test-desc')<br>
   <img src="@field('test-image')" alt=""> <br>

   <br>
    @foreach (($content['test-loop'] ?? []) as $item)
    <br>
        @field('loop-heading', $item)
        @field('loop-desc', $item)
    @endforeach

    <br>
    <a href="@field('test-link')" target="@field('test-link_target')">@field('test-link_label')</a>
</section>