<?php

namespace Tests\Feature;

use App\Livewire\Admin\Settings\Index;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_settings_page_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSee('Global Settings')
            ->assertSee('Home Page')
            ->assertSee('Site Title');
    }

    public function test_can_save_settings(): void
    {
        $page = Page::create(['status' => 'published', 'published_at' => now(), 'order' => 1]);
        $page->translations()->create(['locale' => 'id', 'title' => 'Beranda', 'slug' => 'beranda', 'meta' => []]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('siteTitle', 'Situs Saya')
            ->set('siteFooter', '<p>Footer</p>')
            ->set('homePageId', (string) $page->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Situs Saya', Setting::get('site_title'));
        $this->assertSame('<p>Footer</p>', Setting::get('site_footer'));
        $this->assertSame((string) $page->id, Setting::get('home_page_id'));
    }

    public function test_existing_settings_are_loaded_on_mount(): void
    {
        Setting::set('site_title', 'Lama');

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertSet('siteTitle', 'Lama');
    }

    public function test_site_title_is_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('siteTitle', '')
            ->call('save')
            ->assertHasErrors('siteTitle');
    }

    public function test_home_page_must_be_a_published_page(): void
    {
        $draft = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('homePageId', (string) $draft->id)
            ->call('save')
            ->assertHasErrors('homePageId');
    }
}
