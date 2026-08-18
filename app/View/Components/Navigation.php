<?php

namespace App\View\Components;

use App\Models\Navigation as NavigationModel;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Navigation extends Component
{
    public string $locale;

    public function __construct(public string $slug, ?string $locale = null)
    {
        $this->locale = $locale ?? app()->getLocale();
    }

    protected function navigation(): ?NavigationModel
    {
        return NavigationModel::query()
            ->where('slug', $this->slug)
            ->with([
                'items.children.translations',
                'items.children.page.translations',
                'items.page.translations',
            ])
            ->first();
    }

    public function render(): View
    {
        return view('components.navigation', [
            'navigation' => $this->navigation(),
        ]);
    }
}
