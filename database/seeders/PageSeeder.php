<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $homepage = Page::create([
            'status' => 'published',
            'published_at' => '2026-08-18 06:06:00',
            'order' => 1,
        ]);

        $homepage->translations()->create([
            'locale' => 'id',
            'title' => 'Beranda',
            'slug' => 'beranda',
            'meta' => [
                'meta_title' => 'Content Blog CMS',
                'meta_description' => 'Content Blog CMS',
                'og_image' => 'Beranda',
            ],
        ]);

        $homepage->translations()->create([
            'locale' => 'en',
            'title' => 'Homepage',
            'slug' => 'homepage',
            'meta' => [
                'meta_title' => 'Content Blog CMS',
                'meta_description' => 'Content Blog CMS',
                'og_image' => 'Homepage',
            ],
        ]);

        $goalsPage = Page::create([
            'status' => 'published',
            'published_at' => '2026-08-18 08:03:00',
            'order' => 2,
        ]);

        $goalsPage->translations()->create([
            'locale' => 'id',
            'title' => 'Tujuan Kami',
            'slug' => 'tujuan-kami',
            'meta' => [
                'meta_title' => 'Tujuan Kami',
                'meta_description' => 'Tujuan Kami',
                'og_image' => 'Tujuan Kami',
            ],
        ]);

        $goalsPage->translations()->create([
            'locale' => 'en',
            'title' => 'Our Goals',
            'slug' => 'our-goals',
            'meta' => [
                'meta_title' => 'Our Goals',
                'meta_description' => 'Our Goals',
                'og_image' => 'Our Goals',
            ],
        ]);

        // Set home_page_id setting to point to the homepage
        \App\Models\Setting::set('home_page_id', (string) $homepage->id);
    }
}
