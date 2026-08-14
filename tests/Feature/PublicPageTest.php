<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Section;
use App\Models\SectionType;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    private SectionType $sectionType;

    private array $createdTemplates = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->sectionType = SectionType::create([
            'name' => 'Hero',
            'slug' => 'hero',
            'icon' => 'image',
            'fields' => [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => true],
                ['key' => 'body', 'label' => 'Body', 'type' => 'textarea', 'required' => false],
                ['key' => 'content', 'label' => 'Content', 'type' => 'rich-text', 'required' => false],
                ['key' => 'photo', 'label' => 'Photo', 'type' => 'image', 'required' => false],
                ['key' => 'items', 'label' => 'Items', 'type' => 'repeater', 'required' => false, 'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => false],
                ]],
            ],
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdTemplates as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        parent::tearDown();
    }

    private function writeCustomTemplate(string $markup): void
    {
        $path = resource_path('views/public/sections/hero.blade.php');

        $this->createdTemplates[] = $path;
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $markup);
    }

    private function makePage(string $status = 'published', string $idSlug = 'tentang-kami', string $enSlug = 'about-us'): Page
    {
        $page = Page::create(['status' => $status, 'published_at' => now(), 'order' => 1]);

        $page->translations()->create([
            'locale' => 'id',
            'title' => 'Tentang Kami',
            'slug' => $idSlug,
            'meta' => ['meta_title' => 'Tentang Kami SEO', 'meta_description' => 'Deskripsi singkat', 'og_image' => ''],
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'About Us',
            'slug' => $enSlug,
            'meta' => [],
        ]);

        return $page;
    }

    private function attachSection(Page $page, array $idContent = [], array $enContent = []): Section
    {
        $section = $page->sections()->create(['section_type_id' => $this->sectionType->id, 'order' => 1]);
        $section->translations()->create(['locale' => 'id', 'content' => $idContent]);
        $section->translations()->create(['locale' => 'en', 'content' => $enContent]);

        return $section;
    }

    public function test_published_page_renders_at_locale_prefixed_slug_url(): void
    {
        $page = $this->makePage();
        $this->attachSection($page, ['heading' => 'Selamat Datang']);

        $this->get('/id/tentang-kami')
            ->assertOk()
            ->assertSee('Tentang Kami')
            ->assertSee('Selamat Datang');
    }

    public function test_english_locale_renders_at_prefixed_url(): void
    {
        $page = $this->makePage();
        $this->attachSection($page, ['heading' => 'Selamat Datang'], ['heading' => 'Welcome']);

        $this->get('/en/about-us')
            ->assertOk()
            ->assertSee('About Us')
            ->assertSee('Welcome')
            ->assertDontSee('Selamat Datang');
    }

    public function test_section_with_empty_locale_content_renders_blank(): void
    {
        $page = $this->makePage();
        $this->attachSection($page, ['heading' => 'Selamat Datang'], []);

        $this->get('/en/about-us')
            ->assertOk()
            ->assertSee('About Us')
            ->assertDontSee('Selamat Datang');
    }

    public function test_draft_page_is_not_public(): void
    {
        $page = $this->makePage('draft');

        $this->get('/id/tentang-kami')->assertNotFound();
    }

    public function test_home_renders_page_with_home_slug_when_no_home_setting(): void
    {
        $page = $this->makePage(status: 'published', idSlug: 'beranda');
        $this->attachSection($page, ['heading' => 'Beranda Kita']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Beranda Kita');
    }

    public function test_home_page_slug_is_redirected_when_home_configured(): void
    {
        $page = $this->makePage();
        Setting::set('home_page_id', (string) $page->id);

        $this->get('/id/tentang-kami')
            ->assertRedirect('/id');
    }

    public function test_home_uses_explicitly_configured_home_page(): void
    {
        $first = $this->makePage(status: 'published', idSlug: 'beranda', enSlug: 'home');
        $home = $this->makePage(status: 'published', idSlug: 'homepage', enSlug: 'home-page');
        $this->attachSection($home, ['heading' => 'Beranda Resmi']);

        Setting::set('home_page_id', (string) $home->id);

        $this->get('/')
            ->assertOk()
            ->assertSee('Beranda Resmi');

        $this->get('/id/homepage')->assertRedirect('/id');
        $this->get('/id/beranda')->assertOk();
    }

    public function test_home_falls_back_to_first_published_page(): void
    {
        $page = $this->makePage(status: 'published', idSlug: 'random');
        $this->attachSection($page, ['heading' => 'First Page']);

        $this->get('/')->assertOk()->assertSee('First Page');
    }

    public function test_invalid_locale_returns_not_found(): void
    {
        $page = $this->makePage();

        $this->get('/xx/tentang-kami')->assertNotFound();
        $this->get('/xx')->assertNotFound();
    }

    public function test_smart_headings_rich_text_and_repeater_render(): void
    {
        $page = $this->makePage();
        $this->attachSection($page, [
            'heading' => 'Judul Besar',
            'body' => "Baris satu\nBaris dua",
            'content' => '<h2>Sub Rich</h2><p>Paragraf rich.</p>',
            'items' => [
                ['title' => 'Item Satu'],
                ['title' => 'Item Dua'],
            ],
        ]);

        $this->get('/id/tentang-kami')
            ->assertOk()
            ->assertSee('<h2', false)
            ->assertSee('Judul Besar')
            ->assertSee('Sub Rich')
            ->assertSee('Paragraf rich.')
            ->assertSee('Baris satu')
            ->assertSee('Item Satu')
            ->assertSee('Item Dua');
    }

    public function test_image_field_renders_storage_url(): void
    {
        Storage::fake('public');

        $page = $this->makePage();
        $this->attachSection($page, ['photo' => 'sections/photo.jpg']);

        $this->get('/id/tentang-kami')
            ->assertOk()
            ->assertSee('/storage/sections/photo.jpg');
    }

    public function test_meta_title_and_global_settings_render(): void
    {
        Setting::set('site_title', 'Situs Saya');
        Setting::set('site_footer', '<p>Hak cipta 2026</p>');

        $page = $this->makePage();

        $this->get('/id/tentang-kami')
            ->assertOk()
            ->assertSee('Tentang Kami SEO')
            ->assertSee('Situs Saya')
            ->assertSee('Hak cipta 2026');
    }

    public function test_custom_template_controls_section_rendering(): void
    {
        $this->writeCustomTemplate('<div class="custom-hero">{{ $content["heading"] ?? "" }}</div>');

        $page = $this->makePage();
        $this->attachSection($page, ['heading' => 'Selamat Datang']);

        $this->get('/id/tentang-kami')
            ->assertOk()
            ->assertSee('custom-hero', false)
            ->assertSee('Selamat Datang');
    }

    public function test_custom_template_renders_blank_when_locale_content_is_empty(): void
    {
        $this->writeCustomTemplate('<div class="custom-hero">{{ $content["heading"] ?? "" }}</div>');

        $page = $this->makePage();
        $this->attachSection($page, ['heading' => 'Selamat Datang'], []);

        $this->get('/en/about-us')
            ->assertOk()
            ->assertSee('About Us')
            ->assertDontSee('custom-hero', false)
            ->assertDontSee('Selamat Datang');
    }

    public function test_home_page_slug_still_redirects_when_home_slug_fallback(): void
    {
        $page = $this->makePage(status: 'published', idSlug: 'beranda');
        $this->attachSection($page, ['heading' => 'Home']);

        $this->get('/id/beranda')->assertRedirect('/id');
    }
}
