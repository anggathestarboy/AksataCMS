<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Component;

use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class Create extends PageForm
{
    public function mount(): void
    {
        $this->translations = $this->defaultTranslations();
    }

    public function save()
    {
        $this->validateForm();

        $page = DB::transaction(function (): Page {
            $page = Page::create([
                'status' => $this->status,
                'published_at' => $this->resolvePublishedAt(),
                'order' => (Page::max('order') ?? 0) + 1,
            ]);

            $this->syncTranslations($page);

            return $page;
        });

        session()->flash('status', 'Page created successfully.');

        return redirect()->route('admin.pages.edit', $page);
    }

    public function render()
    {
        return view('livewire.admin.pages.create');
    }
}
