<?php

namespace Tests\Feature;

use App\Livewire\Admin\Pages\Edit;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPageTranslationIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function createPageWithTranslations(): Page
    {
        $page = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
        $page->translations()->create([
            'locale' => 'id',
            'title' => 'Tentang Kami',
            'slug' => 'tentang-kami',
            'meta' => ['meta_title' => 'Meta ID', 'meta_description' => 'Desc ID', 'og_image' => '/img/id.jpg'],
        ]);
        $page->translations()->create([
            'locale' => 'en',
            'title' => 'About Us',
            'slug' => 'about-us',
            'meta' => ['meta_title' => 'Meta EN', 'meta_description' => 'Desc EN', 'og_image' => '/img/en.jpg'],
        ]);

        return $page;
    }

    public function test_editing_en_title_does_not_change_id_title(): void
    {
        $page = $this->createPageWithTranslations();

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $page])
            ->set('activeLocale', 'en')
            ->set('translations.en.title', 'About Us Updated')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Tentang Kami', $page->translations()->where('locale', 'id')->first()->title);
        $this->assertSame('About Us Updated', $page->translations()->where('locale', 'en')->first()->title);
    }

    public function test_editing_en_meta_does_not_change_id_meta(): void
    {
        $page = $this->createPageWithTranslations();

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $page])
            ->set('activeLocale', 'en')
            ->set('translations.en.meta.meta_title', 'Updated Meta EN')
            ->set('translations.en.meta.og_image', '/img/en-updated.jpg')
            ->call('save')
            ->assertHasNoErrors();

        $idTranslation = $page->translations()->where('locale', 'id')->first();
        $enTranslation = $page->translations()->where('locale', 'en')->first();

        $this->assertSame('Meta ID', $idTranslation->meta['meta_title']);
        $this->assertSame('/img/id.jpg', $idTranslation->meta['og_image']);
        $this->assertSame('Updated Meta EN', $enTranslation->meta['meta_title']);
        $this->assertSame('/img/en-updated.jpg', $enTranslation->meta['og_image']);
    }

    public function test_editing_en_title_and_meta_does_not_change_id(): void
    {
        $page = $this->createPageWithTranslations();

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $page])
            ->set('activeLocale', 'en')
            ->set('translations.en.title', 'About Us Updated')
            ->set('translations.en.meta.meta_title', 'Updated Meta EN')
            ->set('translations.en.meta.og_image', '/img/en-updated.jpg')
            ->call('save')
            ->assertHasNoErrors();

        $idTranslation = $page->translations()->where('locale', 'id')->first();

        $this->assertSame('Tentang Kami', $idTranslation->title);
        $this->assertSame('tentang-kami', $idTranslation->slug);
        $this->assertSame('Meta ID', $idTranslation->meta['meta_title']);
        $this->assertSame('Desc ID', $idTranslation->meta['meta_description']);
        $this->assertSame('/img/id.jpg', $idTranslation->meta['og_image']);
    }

    public function test_editing_id_title_does_not_change_en_title(): void
    {
        $page = $this->createPageWithTranslations();

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $page])
            ->set('activeLocale', 'id')
            ->set('translations.id.title', 'Tentang Kami Updated')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('About Us', $page->translations()->where('locale', 'en')->first()->title);
        $this->assertSame('Tentang Kami Updated', $page->translations()->where('locale', 'id')->first()->title);
    }

    public function test_editing_id_meta_does_not_change_en_meta(): void
    {
        $page = $this->createPageWithTranslations();

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $page])
            ->set('activeLocale', 'id')
            ->set('translations.id.meta.meta_title', 'Updated Meta ID')
            ->set('translations.id.meta.og_image', '/img/id-updated.jpg')
            ->call('save')
            ->assertHasNoErrors();

        $idTranslation = $page->translations()->where('locale', 'id')->first();
        $enTranslation = $page->translations()->where('locale', 'en')->first();

        $this->assertSame('Updated Meta ID', $idTranslation->meta['meta_title']);
        $this->assertSame('/img/id-updated.jpg', $idTranslation->meta['og_image']);
        $this->assertSame('Meta EN', $enTranslation->meta['meta_title']);
        $this->assertSame('/img/en.jpg', $enTranslation->meta['og_image']);
    }
}
