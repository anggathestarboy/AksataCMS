<?php

namespace Tests\Feature;

use App\Models\Navigation;
use App\Models\Page;
use App\Models\Section;
use App\Models\SectionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesRenderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_admin_get_routes_render(): void
    {
        $sectionType = SectionType::create(['name' => 'Hero', 'slug' => 'hero', 'icon' => 'image', 'fields' => []]);
        $page = Page::create(['status' => 'draft', 'published_at' => null, 'order' => 1]);
        $section = Section::create(['page_id' => $page->id, 'section_type_id' => $sectionType->id, 'order' => 1]);
        $navigation = Navigation::create(['name' => 'Main Navbar', 'slug' => 'main-navbar']);

        $routes = [
            route('admin.section-types.index'),
            route('admin.section-types.create'),
            route('admin.section-types.edit', $sectionType),
            route('admin.pages.index'),
            route('admin.pages.create'),
            route('admin.pages.edit', $page),
            route('admin.pages.sections.edit', [$page, $section]),
            route('admin.navigations.index'),
            route('admin.navigations.create'),
            route('admin.navigations.edit', $navigation),
            route('admin.navigations.items', $navigation),
        ];

        foreach ($routes as $url) {
            $this->actingAs($this->user)->get($url)->assertOk();
        }
    }

    public function test_admin_routes_require_authentication(): void
    {
        $this->get(route('admin.pages.index'))->assertRedirect(route('login'));
    }
}
