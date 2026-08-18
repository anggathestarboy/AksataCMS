<?php

namespace Tests\Feature;

use App\Livewire\Admin\Navigations\Create;
use App\Livewire\Admin\Navigations\Edit;
use App\Livewire\Admin\Navigations\Index;
use App\Livewire\Admin\Navigations\ItemBuilder;
use App\Models\Navigation;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminNavigationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_can_create_navigation(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Main Navbar')
            ->set('slug', 'main-navbar')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.navigations.edit', Navigation::where('slug', 'main-navbar')->firstOrFail()));

        $this->assertDatabaseHas('navigations', ['name' => 'Main Navbar', 'slug' => 'main-navbar']);
    }

    public function test_slug_is_auto_generated_from_name(): void
    {
        $test = Livewire::actingAs($this->user)->test(Create::class);

        $test->set('name', 'Footer Menu');

        $this->assertSame('footer-menu', $test->get('slug'));
    }

    public function test_slug_must_be_unique(): void
    {
        Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Other Navbar')
            ->set('slug', 'main-navbar')
            ->call('save')
            ->assertHasErrors('slug');
    }

    public function test_slug_format_error_has_clear_message(): void
    {
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('name', 'Main Navbar')
            ->set('slug', 'Main_Navbar')
            ->call('save')
            ->assertHasErrors(['slug' => 'The slug may only contain lowercase letters, numbers and hyphens (e.g. "main-navbar").']);
    }

    public function test_can_edit_navigation(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['navigation' => $navigation])
            ->set('name', 'Header Menu')
            ->set('slug', 'header-menu')
            ->call('save')
            ->assertHasNoErrors();

        $fresh = $navigation->fresh();

        $this->assertSame('Header Menu', $fresh->name);
        $this->assertSame('header-menu', $fresh->slug);
    }

    public function test_edit_cannot_use_slug_of_another_navigation(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);
        Navigation::create(['name' => 'Footer', 'slug' => 'footer']);

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['navigation' => $navigation])
            ->set('slug', 'footer')
            ->call('save')
            ->assertHasErrors('slug');
    }

    public function test_index_lists_and_deletes_navigations(): void
    {
        $alpha = Navigation::create(['name' => 'Alpha', 'slug' => 'alpha']);
        $beta = Navigation::create(['name' => 'Beta', 'slug' => 'beta']);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSee('Alpha')
            ->assertSee('Beta')
            ->call('delete', $alpha->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('navigations', ['id' => $alpha->id]);
        $this->assertDatabaseHas('navigations', ['id' => $beta->id]);
    }

    public function test_deleting_navigation_cascades_to_items_and_translations(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        $item = NavigationItem::create([
            'navigation_id' => $navigation->id,
            'type' => 'external',
            'url' => 'https://example.com',
            'order' => 1,
        ]);

        $item->translations()->create(['locale' => 'id', 'label' => 'Contoh']);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('delete', $navigation->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('navigations', ['id' => $navigation->id]);
        $this->assertDatabaseMissing('navigation_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('navigation_item_translations', ['navigation_item_id' => $item->id]);
    }

    public function test_can_add_external_item_with_labels(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('openCreate')
            ->set('type', 'external')
            ->set('url', 'https://instagram.com/example')
            ->set('openInNewTab', true)
            ->set('labels.id', 'Instagram')
            ->set('labels.en', 'Instagram EN')
            ->call('saveItem')
            ->assertHasNoErrors();

        $item = $navigation->items()->firstOrFail();

        $this->assertSame('external', $item->type);
        $this->assertSame('https://instagram.com/example', $item->url);
        $this->assertTrue((bool) $item->open_in_new_tab);
        $this->assertSame('Instagram', $item->label('id'));
        $this->assertSame('Instagram EN', $item->label('en'));
    }

    public function test_can_add_page_item(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);
        $page = Page::create(['status' => 'published']);
        $page->translations()->create(['locale' => 'id', 'title' => 'Tentang', 'slug' => 'tentang-kami', 'meta' => []]);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('openCreate')
            ->set('type', 'page')
            ->set('pageId', $page->id)
            ->set('labels.id', 'Tentang Kami')
            ->call('saveItem')
            ->assertHasNoErrors();

        $item = $navigation->items()->firstOrFail();

        $this->assertSame('page', $item->type);
        $this->assertSame($page->id, $item->page_id);
        $this->assertNull($item->url);
    }

    public function test_external_item_requires_url(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('openCreate')
            ->set('type', 'external')
            ->set('url', '')
            ->set('labels.id', 'Instagram')
            ->call('saveItem')
            ->assertHasErrors('url');

        $this->assertDatabaseCount('navigation_items', 0);
    }

    public function test_page_item_requires_page(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('openCreate')
            ->set('type', 'page')
            ->set('pageId', null)
            ->set('labels.id', 'Tentang')
            ->call('saveItem')
            ->assertHasErrors('pageId');

        $this->assertDatabaseCount('navigation_items', 0);
    }

    public function test_default_locale_label_is_required(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('openCreate')
            ->set('type', 'external')
            ->set('url', 'https://example.com')
            ->set('labels.id', '')
            ->call('saveItem')
            ->assertHasErrors('labels.id');

        $this->assertDatabaseCount('navigation_items', 0);
    }

    public function test_can_add_submenu_item(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);
        $parent = $navigation->items()->create(['type' => 'internal', 'url' => '/tentang', 'order' => 1]);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('openCreate', $parent->id)
            ->set('type', 'internal')
            ->set('url', '/tim-kami')
            ->set('labels.id', 'Tim Kami')
            ->call('saveItem')
            ->assertHasNoErrors();

        $child = NavigationItem::query()->where('navigation_id', $navigation->id)->where('parent_id', $parent->id)->firstOrFail();

        $this->assertSame('/tim-kami', $child->url);
        $this->assertSame($parent->id, $child->parent_id);
    }

    public function test_can_edit_item(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);
        $item = $navigation->items()->create(['type' => 'external', 'url' => 'https://old.com', 'order' => 1]);
        $item->translations()->create(['locale' => 'id', 'label' => 'Old']);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('openEdit', $item->id)
            ->assertSet('type', 'external')
            ->set('url', 'https://new.com')
            ->set('labels.id', 'New')
            ->call('saveItem')
            ->assertHasNoErrors();

        $fresh = $item->fresh();

        $this->assertSame('https://new.com', $fresh->url);
        $this->assertSame('New', $fresh->label('id'));
    }

    public function test_delete_item_cascades_to_children_and_translations(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);
        $parent = $navigation->items()->create(['type' => 'internal', 'url' => '/parent', 'order' => 1]);
        $child = $navigation->items()->create(['type' => 'internal', 'url' => '/child', 'parent_id' => $parent->id, 'order' => 1]);
        $parent->translations()->create(['locale' => 'id', 'label' => 'Parent']);
        $child->translations()->create(['locale' => 'id', 'label' => 'Child']);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('deleteItem', $parent->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('navigation_items', ['id' => $parent->id]);
        $this->assertDatabaseMissing('navigation_items', ['id' => $child->id]);
        $this->assertDatabaseMissing('navigation_item_translations', ['navigation_item_id' => $parent->id]);
        $this->assertDatabaseMissing('navigation_item_translations', ['navigation_item_id' => $child->id]);
    }

    public function test_move_item_up_and_down(): void
    {
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);
        $a = $navigation->items()->create(['type' => 'internal', 'url' => '/a', 'order' => 1]);
        $b = $navigation->items()->create(['type' => 'internal', 'url' => '/b', 'order' => 2]);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('moveItem', $b->id, 'up')
            ->assertHasNoErrors();

        $this->assertSame(1, $b->fresh()->order);
        $this->assertSame(2, $a->fresh()->order);

        Livewire::actingAs($this->user)
            ->test(ItemBuilder::class, ['navigation' => $navigation])
            ->call('moveItem', $b->id, 'down')
            ->assertHasNoErrors();

        $this->assertSame(2, $b->fresh()->order);
        $this->assertSame(1, $a->fresh()->order);
    }
}
