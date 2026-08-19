{{--
    Template Section: Text + image (text-image)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section class="w-full bg-white py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 items-center">
            <div class="order-1 flex justify-center items-center">
                <img
                    src="@field('image')"
                    alt="-"
                    title="-"
                    width="-"
                    height="-"
                    loading="lazy"
                    decoding="async"
                    class="w-full h-auto rounded-xl shadow-lg"
                >
            </div>

            <div class="order-2 space-y-5">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
                    @field('title')
                </h2>
                <p class="text-gray-700 leading-relaxed">
                    @field('desc')
                </p>
            </div>
        </div>
    </div>
</section>