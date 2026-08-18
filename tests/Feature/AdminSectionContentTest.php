<?php

namespace Tests\Feature;

use App\Livewire\Admin\Pages\Edit;
use App\Livewire\Admin\Pages\SectionEdit;
use App\Models\Page;
use App\Models\Section;
use App\Models\SectionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSectionContentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private SectionType $sectionType;

    private Page $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->sectionType = SectionType::create([
            'name' => 'Hero',
            'slug' => 'hero',
            'icon' => 'image',
            'fields' => [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => true],
            ],
        ]);
        $this->page = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
    }

    private function createSection(): Section
    {
        $section = $this->page->sections()->create(['section_type_id' => $this->sectionType->id, 'order' => 1]);

        foreach (array_keys(config('cms.locales')) as $locale) {
            $section->translations()->create(['locale' => $locale, 'content' => []]);
        }

        return $section;
    }

    public function test_adding_section_creates_translation_rows_for_all_locales(): void
    {
        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $this->page])
            ->set('newSectionTypeId', $this->sectionType->id)
            ->call('addSection')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sections', ['page_id' => $this->page->id, 'section_type_id' => $this->sectionType->id, 'order' => 1]);

        $section = $this->page->sections()->firstOrFail();

        foreach (array_keys(config('cms.locales')) as $locale) {
            $this->assertDatabaseHas('section_translations', ['section_id' => $section->id, 'locale' => $locale]);
        }
    }

    public function test_add_section_requires_a_section_type(): void
    {
        Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $this->page])
            ->call('addSection')
            ->assertHasErrors('newSectionTypeId');
    }

    public function test_can_save_content_per_locale(): void
    {
        $section = $this->createSection();

        Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->set('content.id.heading', 'Selamat Datang')
            ->set('content.en.heading', 'Welcome')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Selamat Datang', $section->translations()->where('locale', 'id')->firstOrFail()->content['heading']);
        $this->assertSame('Welcome', $section->translations()->where('locale', 'en')->firstOrFail()->content['heading']);
    }

    public function test_required_fields_are_validated_in_the_default_locale_only(): void
    {
        $section = $this->createSection();

        Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->call('save')
            ->assertHasErrors('content.id.heading')
            ->assertHasNoErrors('content.en.heading');
    }

    public function test_can_save_with_only_the_default_locale_filled(): void
    {
        $section = $this->createSection();

        Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->set('content.id.heading', 'Selamat Datang')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Selamat Datang', $section->translations()->where('locale', 'id')->firstOrFail()->content['heading']);
    }

    public function test_can_save_rich_text_content(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Rich',
            'slug' => 'rich',
            'icon' => 'document',
            'fields' => [
                ['key' => 'content', 'label' => 'Content', 'type' => 'rich-text', 'required' => true],
            ],
        ]);

        $section = $this->page->sections()->create(['section_type_id' => $sectionType->id, 'order' => 1]);
        $section->translations()->create(['locale' => 'id', 'content' => []]);
        $section->translations()->create(['locale' => 'en', 'content' => []]);

        Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->set('content.id.content', '<h2>Judul</h2><p>Isi.</p>')
            ->set('content.en.content', '<h2>Title</h2><p>Body.</p>')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('<h2>Judul</h2><p>Isi.</p>', $section->translations()->where('locale', 'id')->firstOrFail()->content['content']);
        $this->assertSame('<h2>Title</h2><p>Body.</p>', $section->translations()->where('locale', 'en')->firstOrFail()->content['content']);
    }

    public function test_required_image_field_is_validated_against_the_upload(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Card',
            'slug' => 'card',
            'icon' => 'image',
            'fields' => [
                ['key' => 'card_img', 'label' => 'Card Img', 'type' => 'image', 'required' => true],
            ],
        ]);

        $section = $this->page->sections()->create(['section_type_id' => $sectionType->id, 'order' => 1]);
        $section->translations()->create(['locale' => 'id', 'content' => []]);
        $section->translations()->create(['locale' => 'en', 'content' => []]);

        Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->call('save')
            ->assertHasErrors('content.id.card_img');

        Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->set('uploads.id.card_img', UploadedFile::fake()->image('card.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertStringStartsWith(
            'sections/',
            $section->translations()->where('locale', 'id')->firstOrFail()->content['card_img'],
        );
    }

    public function test_required_field_inside_repeater_is_validated(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Features',
            'slug' => 'features',
            'icon' => 'list',
            'fields' => [
                ['key' => 'items', 'label' => 'Items', 'type' => 'repeater', 'required' => false, 'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                ]],
            ],
        ]);

        $section = $this->page->sections()->create(['section_type_id' => $sectionType->id, 'order' => 1]);
        $section->translations()->create(['locale' => 'id', 'content' => []]);
        $section->translations()->create(['locale' => 'en', 'content' => []]);

        Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->call('addRepeaterItem', 'id', 'items')
            ->call('save')
            ->assertHasErrors('content.id.items.0.title');
    }

    public function test_repeater_buttons_target_locale_relative_paths_in_the_rendered_form(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Features',
            'slug' => 'features',
            'icon' => 'list',
            'fields' => [
                ['key' => 'items', 'label' => 'Items', 'type' => 'repeater', 'required' => false, 'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => false],
                ]],
            ],
        ]);

        $section = $this->page->sections()->create(['section_type_id' => $sectionType->id, 'order' => 1]);
        $section->translations()->create(['locale' => 'id', 'content' => []]);
        $section->translations()->create(['locale' => 'en', 'content' => []]);

        $html = Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section])
            ->html();

        $this->assertStringNotContainsString("addRepeaterItem('id', 'id.items')", $html);
        $this->assertStringContainsString("addRepeaterItem('id', 'items')", $html);
        $this->assertStringNotContainsString("addRepeaterItem('en', 'en.items')", $html);
        $this->assertStringContainsString("addRepeaterItem('en', 'items')", $html);
    }

    public function test_repeater_items_can_be_added_moved_removed_and_saved(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Features',
            'slug' => 'features',
            'icon' => 'list',
            'fields' => [
                ['key' => 'items', 'label' => 'Items', 'type' => 'repeater', 'required' => false, 'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => false],
                ]],
            ],
        ]);

        $section = $this->page->sections()->create(['section_type_id' => $sectionType->id, 'order' => 1]);
        $section->translations()->create(['locale' => 'id', 'content' => []]);
        $section->translations()->create(['locale' => 'en', 'content' => []]);

        $test = Livewire::actingAs($this->user)
            ->test(SectionEdit::class, ['page' => $this->page, 'section' => $section]);

        $test->call('addRepeaterItem', 'id', 'items')
            ->call('addRepeaterItem', 'id', 'items');

        $this->assertCount(2, $test->get('content')['id']['items']);

        $test->set('content.id.items.0.title', 'First')
            ->set('content.id.items.1.title', 'Second')
            ->call('moveRepeaterItem', 'id', 'items', 0, 'down');

        $this->assertSame('Second', $test->get('content')['id']['items'][0]['title']);
        $this->assertSame('First', $test->get('content')['id']['items'][1]['title']);

        $test->call('removeRepeaterItem', 'id', 'items', 1);
        $this->assertCount(1, $test->get('content')['id']['items']);

        $test->call('save')->assertHasNoErrors();

        $this->assertSame('Second', $section->translations()->where('locale', 'id')->firstOrFail()->content['items'][0]['title']);
    }

    public function test_sections_can_be_moved_and_removed(): void
    {
        $sectionA = $this->createSection();
        $sectionB = $this->page->sections()->create(['section_type_id' => $this->sectionType->id, 'order' => 2]);

        foreach (array_keys(config('cms.locales')) as $locale) {
            $sectionB->translations()->create(['locale' => $locale, 'content' => []]);
        }

        $test = Livewire::actingAs($this->user)
            ->test(Edit::class, ['page' => $this->page]);

        $test->call('moveSection', $sectionA->id, 'down');

        $this->assertSame(2, $sectionA->fresh()->order);
        $this->assertSame(1, $sectionB->fresh()->order);

        $test->call('deleteSection', $sectionA->id);

        $this->assertDatabaseMissing('sections', ['id' => $sectionA->id]);
        $this->assertDatabaseMissing('section_translations', ['section_id' => $sectionA->id]);
    }
}
