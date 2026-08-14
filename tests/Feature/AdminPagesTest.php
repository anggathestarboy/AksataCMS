<?php

namespace Tests\Feature;

use App\Livewire\Admin\Pages\Create;
use App\Livewire\Admin\Pages\Edit;
use App\Livewire\Admin\Pages\Index;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_can_create_published_page_with_translations(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'published')
            ->set('translations', [
                'id' => ['title' => 'Tentang Kami', 'slug' => 'tentang-kami', 'meta' => ['meta_title' => 'Tentang', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => 'About Us', 'slug' => 'about-us', 'meta' => ['meta_title' => 'About', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pages', ['status' => 'published']);

        $page = Page::firstOrFail();

        $this->assertNotNull($page->published_at);
        $this->assertSame(1, $page->order);
        $this->assertDatabaseHas('page_translations', ['page_id' => $page->id, 'locale' => 'id', 'title' => 'Tentang Kami', 'slug' => 'tentang-kami']);
        $this->assertDatabaseHas('page_translations', ['page_id' => $page->id, 'locale' => 'en', 'title' => 'About Us', 'slug' => 'about-us']);
        $this->assertSame('Tentang', $page->translations()->where('locale', 'id')->firstOrFail()->meta['meta_title']);
    }

    public function test_draft_page_has_no_published_at(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'draft')
            ->set('translations', [
                'id' => ['title' => 'Beranda', 'slug' => 'beranda', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => 'Home', 'slug' => 'home', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pages', ['status' => 'draft', 'published_at' => null]);
    }

    public function test_can_edit_page_translations(): void
    {
        $page = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
        $page->translations()->create(['locale' => 'id', 'title' => 'Lama', 'slug' => 'lama', 'meta' => []]);
        $page->translations()->create(['locale' => 'en', 'title' => 'Old', 'slug' => 'old', 'meta' => []]);

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $page])
            ->set('translations.id.title', 'Baru')
            ->set('translations.id.slug', 'baru')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Baru', $page->translations()->where('locale', 'id')->firstOrFail()->title);
        $this->assertSame('baru', $page->translations()->where('locale', 'id')->firstOrFail()->slug);
    }

    public function test_slug_must_be_unique_per_locale(): void
    {
        $page = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
        $page->translations()->create(['locale' => 'id', 'title' => 'Tentang', 'slug' => 'tentang-kami', 'meta' => []]);

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('translations', [
                'id' => ['title' => 'X', 'slug' => 'tentang-kami', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => 'Y', 'slug' => 'y', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasErrors('translations.id.slug');
    }

    public function test_same_slug_is_allowed_in_different_locale(): void
    {
        $page = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
        $page->translations()->create(['locale' => 'id', 'title' => 'Tentang', 'slug' => 'tentang-kami', 'meta' => []]);

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('translations', [
                'id' => ['title' => 'Kontak', 'slug' => 'kontak', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => 'About', 'slug' => 'tentang-kami', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pages', ['status' => 'draft']);
    }

    public function test_slug_is_auto_generated_from_title(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->set('translations.id.title', 'Tentang Kami');

        $translations = $test->get('translations');

        $this->assertSame('tentang-kami', $translations['id']['slug']);
    }

    public function test_auto_slug_does_not_override_manual_slug(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->set('translations.id.slug', 'custom-slug');
        $test->set('translations.id.title', 'Tentang Kami');

        $this->assertSame('custom-slug', $test->get('translations')['id']['slug']);
    }

    public function test_slug_format_error_has_clear_message(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'draft')
            ->set('translations', [
                'id' => ['title' => 'X', 'slug' => 'Tentang_Kami', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => 'Y', 'slug' => 'y', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasErrors(['translations.id.slug' => 'The slug may only contain lowercase letters, numbers and hyphens (e.g. "tentang-kami").']);
    }

    public function test_can_create_page_with_default_locale_only(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'draft')
            ->set('translations', [
                'id' => ['title' => 'Beranda', 'slug' => 'beranda', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => '', 'slug' => '', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pages', ['status' => 'draft']);
        $this->assertSame(1, Page::where('status', 'draft')->count());
    }

    public function test_can_create_multiple_pages_with_default_locale_only_without_duplicate_slug_error(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'draft')
            ->set('translations', [
                'id' => ['title' => 'Halaman Pertama', 'slug' => 'halaman-pertama', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => '', 'slug' => '', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasNoErrors();

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'draft')
            ->set('translations', [
                'id' => ['title' => 'Halaman Kedua', 'slug' => 'halaman-kedua', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => '', 'slug' => '', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(2, Page::count());
        $this->assertDatabaseMissing('page_translations', ['locale' => 'en']);
    }

    public function test_extra_locale_title_requires_slug(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'draft')
            ->set('translations', [
                'id' => ['title' => 'Beranda', 'slug' => 'beranda', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => 'About', 'slug' => '', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasErrors(['translations.en.slug' => 'The slug is required in the English locale.']);
    }

    public function test_validation_errors_are_shown_in_summary(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('status', 'draft')
            ->set('translations', [
                'id' => ['title' => '', 'slug' => '', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
                'en' => ['title' => '', 'slug' => '', 'meta' => ['meta_title' => '', 'meta_description' => '', 'og_image' => '']],
            ])
            ->call('save')
            ->assertHasErrors(['translations.id.title', 'translations.id.slug'])
            ->assertSee('Please fix the following before saving:')
            ->assertSee('The title is required in the Indonesian locale.')
            ->assertSee('The slug is required in the Indonesian locale.');
    }

    public function test_index_lists_reorders_and_deletes_pages(): void
    {
        $pageA = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
        $pageB = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 2]);
        $pageA->translations()->create(['locale' => 'id', 'title' => 'Halaman A', 'slug' => 'a', 'meta' => []]);
        $pageB->translations()->create(['locale' => 'id', 'title' => 'Halaman B', 'slug' => 'b', 'meta' => []]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSee('Halaman A')
            ->assertSee('Halaman B')
            ->call('movePage', $pageA->id, 'down');

        $this->assertSame(2, $pageA->fresh()->order);
        $this->assertSame(1, $pageB->fresh()->order);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('delete', $pageA->id);

        $this->assertDatabaseMissing('pages', ['id' => $pageA->id]);
        $this->assertDatabaseHas('pages', ['id' => $pageB->id]);
    }

    public function test_delete_page_removes_its_translations(): void
    {
        $page = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
        $page->translations()->create(['locale' => 'id', 'title' => 'Halaman', 'slug' => 'halaman', 'meta' => []]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('delete', $page->id);

        $this->assertDatabaseMissing('page_translations', ['page_id' => $page->id]);
    }
}
