<?php

namespace App\Livewire\Admin\SectionTypes;

use App\Models\SectionType;
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

    public function delete(int $sectionTypeId): void
    {
        SectionType::findOrFail($sectionTypeId)->delete();

        session()->flash('status', 'Section type deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.section-types.index', [
            'sectionTypes' => SectionType::query()
                ->withCount('sections')
                ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
