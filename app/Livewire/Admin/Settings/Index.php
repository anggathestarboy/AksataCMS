<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    public string $siteTitle = '';

    public string $siteFooter = '';

    public string|TemporaryUploadedFile|null $defaultSeoImage = null;

    public string $homePageId = '';

    public function mount(): void
    {
        $this->siteTitle = (string) Setting::get('site_title', config('app.name', 'ContentBlock'));
        $this->siteFooter = (string) Setting::get('site_footer', '');
        $this->defaultSeoImage = (string) Setting::get('default_seo_image', '');
        $this->homePageId = (string) Setting::get('home_page_id', '');
    }

    public function updatedDefaultSeoImage(): void
    {
        if ($this->defaultSeoImage instanceof TemporaryUploadedFile) {
            $this->validate(['defaultSeoImage' => ['image', 'max:2048']]);
        }
    }

    public function save(): void
    {
        $this->validate([
            'siteTitle' => ['required', 'string', 'max:255'],
            'siteFooter' => ['nullable', 'string'],
            'homePageId' => ['nullable', Rule::exists('pages', 'id')->where('status', 'published')],
        ]);

        if ($this->defaultSeoImage instanceof TemporaryUploadedFile) {
            $this->defaultSeoImage = $this->defaultSeoImage->store('settings', 'public');
        }

        Setting::set('site_title', $this->siteTitle);
        Setting::set('site_footer', $this->siteFooter);
        Setting::set('default_seo_image', (string) ($this->defaultSeoImage ?? ''));
        Setting::set('home_page_id', $this->homePageId);

        session()->flash('status', 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.index', [
            'pages' => Page::query()->where('status', 'published')->orderBy('order')->get(),
        ]);
    }
}
