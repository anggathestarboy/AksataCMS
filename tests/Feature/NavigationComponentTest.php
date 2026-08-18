<?php

namespace Tests\Feature;

use App\Models\Navigation;
use App\Models\NavigationItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_labels_and_urls_for_active_locale(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        $item = NavigationItem::create([
            'navigation_id' => $navigation->id,
            'type' => 'external',
            'url' => 'https://instagram.com/example',
            'open_in_new_tab' => true,
            'order' => 1,
        ]);

        $item->translations()->create(['locale' => 'id', 'label' => 'Instagram']);
        $item->translations()->create(['locale' => 'en', 'label' => 'Instagram EN']);

        $html = $this->blade('<x-navigation :slug="$slug" :locale="$locale" />', [
            'slug' => 'main-navbar',
            'locale' => 'en',
        ]);

        $html->assertSee('Instagram EN');
        $html->assertSee('https://instagram.com/example');
        $html->assertSee('target="_blank"', false);
        $html->assertDontSee('Instagram</a>', false);
    }

    public function test_falls_back_to_default_locale_label_when_active_locale_missing(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        $item = NavigationItem::create([
            'navigation_id' => $navigation->id,
            'type' => 'internal',
            'url' => '/kontak',
            'order' => 1,
        ]);

        $item->translations()->create(['locale' => 'id', 'label' => 'Kontak']);

        $this->blade('<x-navigation :slug="$slug" :locale="$locale" />', [
            'slug' => 'main-navbar',
            'locale' => 'en',
        ])->assertSee('Kontak');
    }

    public function test_renders_page_items_with_resolved_url(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        $page = Page::create(['status' => 'published']);
        $page->translations()->create(['locale' => 'id', 'title' => 'Tentang', 'slug' => 'tentang-kami', 'meta' => []]);
        $page->translations()->create(['locale' => 'en', 'title' => 'About', 'slug' => 'about-us', 'meta' => []]);

        $item = NavigationItem::create([
            'navigation_id' => $navigation->id,
            'type' => 'page',
            'page_id' => $page->id,
            'order' => 1,
        ]);

        $item->translations()->create(['locale' => 'id', 'label' => 'Tentang Kami']);
        $item->translations()->create(['locale' => 'en', 'label' => 'About Us']);

        $html = $this->blade('<x-navigation :slug="$slug" :locale="$locale" />', [
            'slug' => 'main-navbar',
            'locale' => 'en',
        ]);

        $html->assertSee('About Us');
        $html->assertSee('/en/about-us');
        $html->assertDontSee('/en/tentang-kami');
    }

    public function test_renders_nested_submenu_items(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        $parent = NavigationItem::create([
            'navigation_id' => $navigation->id,
            'type' => 'internal',
            'url' => '#',
            'order' => 1,
        ]);

        $child = NavigationItem::create([
            'navigation_id' => $navigation->id,
            'type' => 'internal',
            'url' => '/tim-kami',
            'parent_id' => $parent->id,
            'order' => 1,
        ]);

        $parent->translations()->create(['locale' => 'id', 'label' => 'Perusahaan']);
        $child->translations()->create(['locale' => 'id', 'label' => 'Tim Kami']);

        $html = $this->blade('<x-navigation :slug="$slug" :locale="$locale" />', [
            'slug' => 'main-navbar',
            'locale' => 'id',
        ]);

        $html->assertSee('Perusahaan');
        $html->assertSee('Tim Kami');
        $html->assertSee('/tim-kami');
    }

    public function test_renders_nothing_when_navigation_slug_does_not_exist(): void
    {
        $this->blade('<x-navigation :slug="$slug" :locale="$locale" />', [
            'slug' => 'does-not-exist',
            'locale' => 'id',
        ])->assertDontSee('<nav', false);
    }
}
