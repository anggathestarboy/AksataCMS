<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\SectionType;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $homepage = PageTranslation::where('slug', 'beranda')->first()->page;
        $goalsPage = PageTranslation::where('slug', 'tujuan-kami')->first()->page;

        $heroType = SectionType::where('slug', 'hero-section')->first();
        $textImageType = SectionType::where('slug', 'text-image')->first();
        $cardType = SectionType::where('slug', 'card')->first();

        // Homepage - Hero Section (order 1)
        $hero = $homepage->sections()->create([
            'section_type_id' => $heroType->id,
            'order' => 1,
        ]);
        $hero->translations()->create([
            'locale' => 'id',
            'content' => [
                'title' => 'Content Blog CMS',
                'desc' => 'Content Blog CMS adalah tempat dimana kamu bisa memanajemani konten dengan terstruktur menggunakan konsep Page Builder',
                'image' => 'sections/grZJzjkQdT2oh0fGKtAm2lC3uCDL1errtaz5Rz65.webp',
            ],
        ]);
        $hero->translations()->create([
            'locale' => 'en',
            'content' => [
                'title' => 'Content Blog CMS',
                'desc' => 'Content Blog CMS is a place where you can manage content in a structured way using the Page Builder concept.',
                'image' => 'sections/grZJzjkQdT2oh0fGKtAm2lC3uCDL1errtaz5Rz65.webp',
            ],
        ]);

        // Homepage - Text + Image (order 2)
        $textImage = $homepage->sections()->create([
            'section_type_id' => $textImageType->id,
            'order' => 2,
        ]);
        $textImage->translations()->create([
            'locale' => 'id',
            'content' => [
                'title' => 'Mengapa Memilih Kami?',
                'desc' => 'Kami menggunakan Laravel 13 sebagai dasar pengembangan untuk menghadirkan CMS yang modern, mudah dikelola, dan fleksibel sesuai kebutuhan. Dengan pendekatan page builder, Anda dapat menyusun, mengubah, dan menyesuaikan halaman website dengan lebih leluasa tanpa perlu memahami hal-hal teknis yang rumit. Hasilnya, website menjadi lebih mudah dikelola dan dapat berkembang mengikuti kebutuhan Anda.',
                'image' => 'sections/6s1onnqWB7b8qSUW39hNC35SCUCPg8vwQsRJHx0w.webp',
            ],
        ]);
        $textImage->translations()->create([
            'locale' => 'en',
            'content' => [
                'title' => 'Why Choose Us?',
                'desc' => 'We use Laravel 13 as our development platform to deliver a modern, easy-to-manage, and flexible CMS tailored to your needs. With a page builder approach, you can easily create, modify, and customize website pages without the need for complex technical knowledge. As a result, your website is easier to manage and can grow with your needs.',
                'image' => 'sections/6s1onnqWB7b8qSUW39hNC35SCUCPg8vwQsRJHx0w.webp',
            ],
        ]);

        // Homepage - Card (order 4)
        $card = $homepage->sections()->create([
            'section_type_id' => $cardType->id,
            'order' => 4,
        ]);
        $card->translations()->create([
            'locale' => 'id',
            'content' => [
                'card-heading' => 'Apa saja fitur yang kami sediakan?',
                'card-desc' => 'Kami menyediakan berbagai fitur untuk memudahkan pengelolaan website, mulai dari manajemen section, page, dan menu, hingga dukungan multi-bahasa yang memungkinkan konten disesuaikan untuk berbagai bahasa dengan lebih mudah.',
                'list-card' => [
                    [
                        'heading-card' => 'Manajamen Navigasi',
                        'desc-card' => 'Di sini kamu dapat membuat dan mengatur berbagai navigasi website seperti header, sidebar, dan menu lainnya dengan mudah, fleksibel, dan sesuai kebutuhan.',
                        'image-card' => '',
                        'link' => [
                            'url' => '',
                            'label' => '',
                            'page_id' => null,
                            'link_type' => 'internal',
                            'open_in_new_tab' => false,
                        ],
                    ],
                    [
                        'heading-card' => 'Manajamen Section',
                        'desc-card' => 'Buat section sesuai kebutuhanmu, pilih type yang tersedia, lalu sesuaikan tampilannya dengan mudah pada file blade yang sudah disediakan',
                        'image-card' => '',
                        'link' => [
                            'url' => '',
                            'label' => '',
                            'page_id' => null,
                            'link_type' => 'internal',
                            'open_in_new_tab' => false,
                        ],
                    ],
                    [
                        'heading-card' => 'Manajamen Halaman',
                        'desc-card' => 'Di sini kamu dapat membuat dan mengelola halaman dengan mudah, menyusun isinya menggunakan section yang telah dibuat, serta mengatur konten dalam berbagai bahasa sesuai kebutuhan.',
                        'image-card' => '',
                        'link' => [
                            'url' => '',
                            'label' => '',
                            'page_id' => null,
                            'link_type' => 'internal',
                            'open_in_new_tab' => false,
                        ],
                    ],
                ],
            ],
        ]);
        $card->translations()->create([
            'locale' => 'en',
            'content' => [
                'card-heading' => 'What features do we provide?',
                'card-desc' => 'We provide various features to make website management easier, from section, page, and menu management, to multi-language support that allows content to be adapted to various languages more easily.',
                'list-card' => [
                    [
                        'heading-card' => 'Navigation Management',
                        'desc-card' => 'Here you can create and organize various website navigation such as headers, sidebars, and other menus easily, flexibly, and according to your needs.',
                        'image-card' => '',
                        'link' => [
                            'url' => '',
                            'label' => '',
                            'page_id' => null,
                            'link_type' => 'internal',
                            'open_in_new_tab' => false,
                        ],
                    ],
                    [
                        'heading-card' => 'Management Section',
                        'desc-card' => 'Create sections according to your needs, select the available types, then easily customize the appearance in the blade file provided.',
                        'image-card' => '',
                        'link' => [
                            'url' => '',
                            'label' => '',
                            'page_id' => null,
                            'link_type' => 'internal',
                            'open_in_new_tab' => false,
                        ],
                    ],
                    [
                        'heading-card' => 'Page Management',
                        'desc-card' => 'Here you can easily create and manage pages, organize their content using pre-created sections, and organize content in multiple languages as needed.',
                        'image-card' => '',
                        'link' => [
                            'url' => '',
                            'label' => '',
                            'page_id' => null,
                            'link_type' => 'internal',
                            'open_in_new_tab' => false,
                        ],
                    ],
                ],
            ],
        ]);

        // Goals Page - Hero Section (order 1)
        $goalsHero = $goalsPage->sections()->create([
            'section_type_id' => $heroType->id,
            'order' => 1,
        ]);
        $goalsHero->translations()->create([
            'locale' => 'id',
            'content' => [
                'title' => 'Tujuan Kami',
                'desc' => 'Eksplorasi tentang tujuan kami mengembangkan CMS ini',
                'image' => 'sections/siNfEmaEIqD4no0Xn65jD2JcETr6LWH333y0utYK.webp',
            ],
        ]);
        $goalsHero->translations()->create([
            'locale' => 'en',
            'content' => [
                'title' => 'Our Goal',
                'desc' => 'Exploration of our goals in developing this CMS',
                'image' => 'sections/siNfEmaEIqD4no0Xn65jD2JcETr6LWH333y0utYK.webp',
            ],
        ]);

        // Goals Page - Text + Image (order 2)
        $goalsTextImage = $goalsPage->sections()->create([
            'section_type_id' => $textImageType->id,
            'order' => 2,
        ]);
        $goalsTextImage->translations()->create([
            'locale' => 'id',
            'content' => [
                'title' => 'Apa Tujuan CMS Ini?',
                'desc' => 'CMS ini dibuat untuk memberikan cara yang lebih sederhana dan fleksibel dalam membangun serta mengelola website, sehingga Anda dapat mengatur halaman, konten, dan navigasi dengan mudah tanpa harus selalu bergantung pada proses teknis yang rumit.',
                'image' => 'sections/ncXAGAwSMWy2eBIMm8ajkPyfdMpo4kOuQwaDyVbO.webp',
            ],
        ]);
        $goalsTextImage->translations()->create([
            'locale' => 'en',
            'content' => [
                'title' => 'What is the Purpose of This CMS?',
                'desc' => 'This CMS was created to provide a simpler and more flexible way to build and manage websites, so you can easily organize pages, content, and navigation without having to always rely on complicated technical processes.',
                'image' => 'sections/ncXAGAwSMWy2eBIMm8ajkPyfdMpo4kOuQwaDyVbO.webp',
            ],
        ]);
    }
}
