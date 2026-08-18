<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function movePage(int $pageId, string $direction): void
    {
        $pages = Page::query()->orderBy('order')->get();
        $page = $pages->firstWhere('id', $pageId);

        if ($page === null) {
            return;
        }

        $index = $pages->search(fn (Page $item): bool => $item->id === $pageId);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $pages->count()) {
            return;
        }

        $other = $pages[$target];

        $pageOrder = $page->order;
        $otherOrder = $other->order;

        $page->update(['order' => $otherOrder]);
        $other->update(['order' => $pageOrder]);
    }

    public function delete(int $pageId): void
    {
        Page::findOrFail($pageId)->delete();

        session()->flash('status', 'Page deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.pages.index', [
            'pages' => Page::query()
                ->with('translations')
                ->withCount('sections')
                ->when($this->search, fn ($query) => $query->whereHas('translations', fn ($t) => $t->where('title', 'like', "%{$this->search}%")))
                ->orderBy('order')
                ->paginate(10),
        ]);
    }
}
