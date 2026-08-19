{{-- 
    Template Section: Hero Section (hero-section)
    =========================================================
    Variabel: $sectionType, $section, $locale, $content, $fields
    =========================================================
--}}

<section class="relative w-full bg-gray-900 overflow-hidden">
    <img
        src="@field('image')"
        alt="-"
        title="-"
        width="-"
        height="-"
        loading="lazy"
        decoding="async"
        class="absolute inset-0 w-full h-full object-cover"
    >

    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-36 text-left">
        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight my-3">
            @field('title')
        </h1>

        <p class="my-3 text-base sm:text-lg text-gray-100 whitespace-pre-line leading-relaxed max-w-2xl">
            @field('desc')
        </p>
    </div>
</section>