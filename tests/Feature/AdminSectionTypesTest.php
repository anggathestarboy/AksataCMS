<?php

namespace Tests\Feature;

use App\Livewire\Admin\SectionTypes\Create;
use App\Livewire\Admin\SectionTypes\Edit;
use App\Livewire\Admin\SectionTypes\Index;
use App\Models\SectionType;
use App\Models\User;
use App\Services\SectionTemplateGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSectionTypesTest extends TestCase
{
    use RefreshDatabase;

    private array $createdTemplates = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
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

    private function templatePath(string $slug): string
    {
        $path = app(SectionTemplateGenerator::class)->path($slug);

        $this->createdTemplates[] = $path;

        return $path;
    }

    public function test_can_create_section_type_with_nested_repeater_fields(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Hero Banner')
            ->set('slug', 'hero-banner')
            ->set('icon', 'sparkles')
            ->set('fields', [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => true],
                ['key' => 'cta-items', 'label' => 'CTA Buttons', 'type' => 'repeater', 'required' => false, 'fields' => [
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'required' => true],
                    ['key' => 'url', 'label' => 'URL', 'type' => 'text', 'required' => true],
                ]],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.section-types.index'));

        $this->templatePath('hero-banner');

        $this->assertDatabaseHas('section_types', ['slug' => 'hero-banner']);

        $sectionType = SectionType::where('slug', 'hero-banner')->firstOrFail();

        $this->assertSame('Hero Banner', $sectionType->name);
        $this->assertSame('sparkles', $sectionType->icon);
        $this->assertSame([
            'key' => 'cta-items',
            'label' => 'CTA Buttons',
            'type' => 'repeater',
            'required' => false,
            'fields' => [
                ['key' => 'label', 'label' => 'Label', 'type' => 'text', 'required' => true],
                ['key' => 'url', 'label' => 'URL', 'type' => 'text', 'required' => true],
            ],
        ], $sectionType->fields[1]);
    }

    public function test_can_create_section_type_with_rich_text_field(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Article')
            ->set('slug', 'article')
            ->set('icon', 'document')
            ->set('fields', [
                ['key' => 'content', 'label' => 'Content', 'type' => 'rich-text', 'required' => true],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.section-types.index'));

        $this->templatePath('article');

        $this->assertSame('rich-text', SectionType::where('slug', 'article')->firstOrFail()->fields[0]['type']);
    }

    public function test_slug_must_be_unique(): void
    {
        SectionType::create(['name' => 'Hero', 'slug' => 'hero-banner', 'icon' => 'image', 'fields' => []]);

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Other Banner')
            ->set('slug', 'hero-banner')
            ->set('icon', 'image')
            ->call('save')
            ->assertHasErrors('slug');
    }

    public function test_can_edit_section_type(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Hero',
            'slug' => 'hero-banner',
            'icon' => 'image',
            'fields' => [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => false],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['sectionType' => $sectionType])
            ->set('name', 'Hero Banner 2')
            ->set('fields', [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'textarea', 'required' => true],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.section-types.edit', $sectionType));

        $this->templatePath('hero-banner');

        $fresh = $sectionType->fresh();

        $this->assertSame('Hero Banner 2', $fresh->name);
        $this->assertSame('textarea', $fresh->fields[0]['type']);
        $this->assertTrue($fresh->fields[0]['required']);
    }

    public function test_edit_cannot_use_slug_of_another_section_type(): void
    {
        $sectionType = SectionType::create(['name' => 'Hero', 'slug' => 'hero-banner', 'icon' => 'image', 'fields' => []]);
        SectionType::create(['name' => 'Gallery', 'slug' => 'gallery', 'icon' => 'image', 'fields' => []]);

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['sectionType' => $sectionType])
            ->set('slug', 'gallery')
            ->call('save')
            ->assertHasErrors('slug');
    }

    public function test_key_is_auto_generated_from_label(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->call('addField');
        $test->set('fields.0.label', 'CTA Button');

        $this->assertSame('cta-button', $test->get('fields')[0]['key']);
    }

    public function test_auto_key_does_not_override_manual_key(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->call('addField');
        $test->set('fields.0.key', 'custom');
        $test->set('fields.0.label', 'Some Label');

        $this->assertSame('custom', $test->get('fields')[0]['key']);
    }

    public function test_save_without_manual_keys_auto_generates_them(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Hero')
            ->set('slug', 'hero')
            ->set('icon', 'image')
            ->call('addField')
            ->set('fields.0.label', 'Heading')
            ->set('fields.0.type', 'text')
            ->call('save')
            ->assertHasNoErrors();

        $this->templatePath('hero');

        $sectionType = SectionType::where('slug', 'hero')->firstOrFail();

        $this->assertSame('heading', $sectionType->fields[0]['key']);
        $this->assertSame('Heading', $sectionType->fields[0]['label']);
    }

    public function test_key_format_error_has_clear_message(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Hero')
            ->set('slug', 'hero')
            ->set('icon', 'image')
            ->call('addField')
            ->set('fields.0.key', 'Heading_Text')
            ->set('fields.0.label', 'Heading Text')
            ->call('save')
            ->assertHasErrors(['fields.0.key' => 'The key may only contain lowercase letters, numbers and hyphens (e.g. "heading-text").']);
    }

    public function test_top_level_fields_can_be_moved(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->call('addField');
        $test->call('addField');
        $test->set('fields.0.label', 'First');
        $test->set('fields.1.label', 'Second');

        $test->call('moveField', '1', 'up');

        $this->assertSame('Second', $test->get('fields')[0]['label']);
        $this->assertSame('First', $test->get('fields')[1]['label']);

        $test->call('moveField', '1', 'down');

        $this->assertSame('Second', $test->get('fields')[0]['label']);
        $this->assertSame('First', $test->get('fields')[1]['label']);
    }

    public function test_nested_fields_can_be_moved(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->call('addField');
        $test->set('fields.0.type', 'repeater');
        $test->call('addField', '0.fields');
        $test->call('addField', '0.fields');
        $test->set('fields.0.fields.0.label', 'Item One');
        $test->set('fields.0.fields.1.label', 'Item Two');

        $test->call('moveField', '0.fields.1', 'up');

        $this->assertSame('Item Two', $test->get('fields')[0]['fields'][0]['label']);
        $this->assertSame('Item One', $test->get('fields')[0]['fields'][1]['label']);
    }

    public function test_top_level_fields_can_be_removed(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->call('addField');
        $test->call('addField');
        $test->call('addField');
        $test->set('fields.0.label', 'A');
        $test->set('fields.1.label', 'B');
        $test->set('fields.2.label', 'C');

        $test->call('removeField', '1');

        $fields = $test->get('fields');

        $this->assertCount(2, $fields);
        $this->assertSame('A', $fields[0]['label']);
        $this->assertSame('C', $fields[1]['label']);
    }

    public function test_index_lists_and_deletes_section_types(): void
    {
        $alpha = SectionType::create(['name' => 'Alpha', 'slug' => 'alpha', 'icon' => 'image', 'fields' => []]);
        $beta = SectionType::create(['name' => 'Beta', 'slug' => 'beta', 'icon' => 'image', 'fields' => []]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSee('Alpha')
            ->assertSee('Beta')
            ->call('delete', $alpha->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('section_types', ['id' => $alpha->id]);
        $this->assertDatabaseHas('section_types', ['id' => $beta->id]);
    }

    public function test_creating_section_type_generates_a_blade_template_file(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Hero Banner')
            ->set('slug', 'hero-banner')
            ->set('icon', 'sparkles')
            ->set('fields', [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => true],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.section-types.index'));

        $path = $this->templatePath('hero-banner');

        $this->assertFileExists($path);
        $this->assertStringContainsString('Template Section: Hero Banner (hero-banner)', File::get($path));
        $this->assertStringContainsString("{{ \$content['heading'] ?? '' }}", File::get($path));
    }

    public function test_renaming_section_type_slug_renames_the_template_file(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Hero',
            'slug' => 'hero-banner',
            'icon' => 'image',
            'fields' => [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => false],
            ],
        ]);

        $oldPath = $this->templatePath('hero-banner');
        $newPath = $this->templatePath('hero-banner-2');

        File::ensureDirectoryExists(dirname($oldPath));
        File::put($oldPath, 'CUSTOM OLD MARKER');

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['sectionType' => $sectionType])
            ->set('slug', 'hero-banner-2')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.section-types.edit', $sectionType));

        $this->assertFileDoesNotExist($oldPath);
        $this->assertFileExists($newPath);
        $this->assertStringContainsString('CUSTOM OLD MARKER', File::get($newPath));
    }

    public function test_editing_without_slug_change_preserves_customized_template(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Hero',
            'slug' => 'hero',
            'icon' => 'image',
            'fields' => [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => false],
            ],
        ]);

        $path = $this->templatePath('hero');

        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'CUSTOM MARKER');

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['sectionType' => $sectionType])
            ->set('name', 'Hero Renamed')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertStringContainsString('CUSTOM MARKER', File::get($path));
    }

    public function test_regenerate_template_overwrites_customizations(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Hero',
            'slug' => 'hero',
            'icon' => 'image',
            'fields' => [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => false],
            ],
        ]);

        $path = $this->templatePath('hero');

        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'CUSTOM MARKER');

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['sectionType' => $sectionType])
            ->call('regenerateTemplate')
            ->assertHasNoErrors();

        $content = File::get($path);

        $this->assertStringNotContainsString('CUSTOM MARKER', $content);
        $this->assertStringContainsString('Template Section: Hero (hero)', $content);
    }

    public function test_deleting_section_type_removes_the_template_file(): void
    {
        $sectionType = SectionType::create([
            'name' => 'Hero',
            'slug' => 'hero',
            'icon' => 'image',
            'fields' => [
                ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'required' => false],
            ],
        ]);

        $generator = app(SectionTemplateGenerator::class);
        $generator->generate($sectionType);
        $path = $generator->path('hero');
        $this->createdTemplates[] = $path;
        $this->assertFileExists($path);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('delete', $sectionType->id)
            ->assertHasNoErrors();

        $this->assertFileDoesNotExist($path);
    }
}
