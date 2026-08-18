<?php

namespace App\Livewire\Admin\Navigations;

use App\Models\Navigation;
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

    public function delete(int $navigationId): void
    {
        Navigation::findOrFail($navigationId)->delete();

        session()->flash('status', 'Navigation deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.navigations.index', [
            'navigations' => Navigation::query()
                ->withCount('items')
                ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
