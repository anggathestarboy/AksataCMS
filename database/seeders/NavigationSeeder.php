<?php

namespace Database\Seeders;

use App\Models\Navigation;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $navbar = Navigation::create([
            'name' => 'navbar',
            'slug' => 'navbar',
        ]);

        $homepage = PageTranslation::where('slug', 'beranda')->first()->page;
        $goalsPage = PageTranslation::where('slug', 'tujuan-kami')->first()->page;

        // Beranda (direct link to homepage)
        $beranda = $navbar->items()->create([
            'parent_id' => null,
            'type' => 'page',
            'url' => null,
            'page_id' => $homepage->id,
            'icon' => null,
            'open_in_new_tab' => false,
            'order' => 1,
        ]);
        $beranda->translations()->create(['locale' => 'id', 'label' => 'Beranda']);
        $beranda->translations()->create(['locale' => 'en', 'label' => 'Home']);

        // Tentang Kami (parent dropdown, no page link)
        $tentangKami = $navbar->items()->create([
            'parent_id' => null,
            'type' => 'page',
            'url' => null,
            'page_id' => null,
            'icon' => null,
            'open_in_new_tab' => false,
            'order' => 2,
        ]);
        $tentangKami->translations()->create(['locale' => 'id', 'label' => 'Tentang Kami']);
        $tentangKami->translations()->create(['locale' => 'en', 'label' => 'About Us']);

        // Tujuan Kami (child of Tentang Kami)
        $tujuanKami = $navbar->items()->create([
            'parent_id' => $tentangKami->id,
            'type' => 'page',
            'url' => null,
            'page_id' => $goalsPage->id,
            'icon' => null,
            'open_in_new_tab' => false,
            'order' => 1,
        ]);
        $tujuanKami->translations()->create(['locale' => 'id', 'label' => 'Tujuan Kami']);
        $tujuanKami->translations()->create(['locale' => 'en', 'label' => 'Our Goal']);
    }
}
