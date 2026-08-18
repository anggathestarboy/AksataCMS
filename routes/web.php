<?php

use App\Http\Controllers\PublicPageController;
use App\Livewire\Admin\Navigations\Create as NavigationCreate;
use App\Livewire\Admin\Navigations\Edit as NavigationEdit;
use App\Livewire\Admin\Navigations\Index as NavigationIndex;
use App\Livewire\Admin\Navigations\ItemBuilder as NavigationItemBuilder;
use App\Livewire\Admin\Pages\Create as PageCreate;
use App\Livewire\Admin\Pages\Edit as PageEdit;
use App\Livewire\Admin\Pages\Index as PageIndex;
use App\Livewire\Admin\Pages\SectionEdit;
use App\Livewire\Admin\SectionTypes\Create as SectionTypeCreate;
use App\Livewire\Admin\SectionTypes\Edit as SectionTypeEdit;
use App\Livewire\Admin\SectionTypes\Index as SectionTypeIndex;
use App\Livewire\Admin\Settings\Index as SettingsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('section-types', SectionTypeIndex::class)->name('section-types.index');
        Route::get('section-types/create', SectionTypeCreate::class)->name('section-types.create');
        Route::get('section-types/{sectionType}/edit', SectionTypeEdit::class)->name('section-types.edit');

        Route::get('pages', PageIndex::class)->name('pages.index');
        Route::get('pages/create', PageCreate::class)->name('pages.create');
        Route::get('pages/{page}/edit', PageEdit::class)->name('pages.edit');
        Route::get('pages/{page}/sections/{section}/edit', SectionEdit::class)->name('pages.sections.edit');

        Route::get('navigations', NavigationIndex::class)->name('navigations.index');
        Route::get('navigations/create', NavigationCreate::class)->name('navigations.create');
        Route::get('navigations/{navigation}/edit', NavigationEdit::class)->name('navigations.edit');
        Route::get('navigations/{navigation}/items', NavigationItemBuilder::class)->name('navigations.items');

        Route::get('settings', SettingsIndex::class)->name('settings.index');
    });
});

Route::get('/{locale}', [PublicPageController::class, 'home'])->name('home.locale');
Route::get('/{locale}/{slug}', [PublicPageController::class, 'show'])->name('public.page');
