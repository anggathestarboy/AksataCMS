<?php

namespace Tests\Feature;

use App\Livewire\Admin\SectionTypes\Create;
use App\Livewire\Admin\SectionTypes\Edit;
use App\Livewire\Admin\SectionTypes\Index;
use App\Models\SectionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSectionTypesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
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
}
