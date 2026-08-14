<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(?string $locale = null): View
    {
        $locale ??= config('cms.default_locale');

        abort_unless(array_key_exists($locale, config('cms.locales')), 404);

        $page = $this->resolveHomePage();

        if ($page === null) {
            abort(404);
        }

        return $this->render($page, $locale);
    }

    public function show(string $locale, string $slug): View|RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('cms.locales')), 404);

        $page = Page::query()
            ->where('status', 'published')
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale)->where('slug', $slug))
            ->first();

        if ($page === null) {
            abort(404);
        }

        if ($this->isHomePage($page)) {
            return redirect()->route('home.locale', ['locale' => $locale]);
        }

        return $this->render($page, $locale);
    }

    protected function resolveHomePage(): ?Page
    {
        $homePageId = (string) Setting::get('home_page_id', '');

        if ($homePageId !== '') {
            $page = Page::query()->where('id', $homePageId)->where('status', 'published')->first();

            if ($page !== null) {
                return $page;
            }
        }

        $homeSlug = (string) config('cms.home_slug', '');

        if ($homeSlug !== '') {
            $page = Page::query()
                ->where('status', 'published')
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', config('cms.default_locale'))
                    ->where('slug', $homeSlug))
                ->first();

            if ($page !== null) {
                return $page;
            }
        }

        return Page::query()->where('status', 'published')->orderBy('order')->first();
    }

    protected function isHomePage(Page $page): bool
    {
        $homePageId = (string) Setting::get('home_page_id', '');

        if ($homePageId !== '') {
            return (string) $page->getKey() === $homePageId;
        }

        $homeSlug = (string) config('cms.home_slug', '');

        if ($homeSlug === '') {
            return false;
        }

        $translation = $page->translation(config('cms.default_locale'));

        return $translation !== null && $translation->slug === $homeSlug;
    }

    protected function render(Page $page, string $locale): View
    {
        $page->load([
            'translations',
            'sections.sectionType',
            'sections.translations',
        ]);

        $translation = $page->translation($locale);

        if ($translation === null) {
            abort(404);
        }

        return view('public.page', [
            'page' => $page,
            'locale' => $locale,
            'translation' => $translation,
        ]);
    }
}
