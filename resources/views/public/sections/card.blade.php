{{--
    Template Section: card (card)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section class="w-full bg-white py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-left mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                @field('card-heading')
            </h2>

            <p class="mt-3 text-gray-700">
                @field('card-desc')
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @repeater('list-card')
                <div class="rounded-lg border border-gray-500 p-4">

                    <h3 class="text-xl font-semibold text-gray-800">
                        @field('heading-card')
                    </h3>

                    <p class="mt-2 text-gray-700 leading-relaxed">
                        @field('desc-card')
                    </p>

                  
                </div>
            @endrepeater
        </div>
    </div>
</section>