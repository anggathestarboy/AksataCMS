<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Models\Section;
use App\Models\SectionType;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Edit extends PageForm
{
    public ?int $newSectionTypeId = null;

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->status = $page->status;
        $this->publishedAt = $page->published_at?->format('Y-m-d\TH:i');
        $this->translations = $this->defaultTranslations();

        foreach ($page->translations as $translation) {
            $this->translations[$translation->locale] = [
                'title' => $translation->title,
                'slug' => $translation->slug,
                'meta' => array_replace($this->defaultMeta(), $translation->meta ?? []),
            ];
        }
    }

    public function save()
    {
        $this->validateForm();

        $this->page->update([
            'status' => $this->status,
            'published_at' => $this->resolvePublishedAt(),
        ]);

        $this->syncTranslations($this->page);

        session()->flash('status', 'Page updated successfully.');

        return redirect()->route('admin.pages.edit', $this->page);
    }

    public function delete()
    {
        $this->page->delete();

        session()->flash('status', 'Page deleted successfully.');

        return redirect()->route('admin.pages.index');
    }

    public function addSection(): void
    {
        if ($this->newSectionTypeId === null) {
            $this->addError('newSectionTypeId', 'Please choose a section type.');

            throw ValidationException::withMessages($this->getErrorBag()->toArray());
        }

        $sectionType = SectionType::findOrFail($this->newSectionTypeId);

        $section = $this->page->sections()->create([
            'section_type_id' => $sectionType->id,
            'order' => ($this->page->sections()->max('order') ?? 0) + 1,
        ]);

        foreach (array_keys(config('cms.locales')) as $locale) {
            $section->translations()->create([
                'locale' => $locale,
                'content' => [],
            ]);
        }

        $this->newSectionTypeId = null;
        $this->resetValidation('newSectionTypeId');
    }

    public function moveSection(int $sectionId, string $direction): void
    {
        $sections = $this->page->sections()->get();
        $section = $sections->firstWhere('id', $sectionId);

        if ($section === null) {
            return;
        }

        $index = $sections->search(fn (Section $item): bool => $item->id === $sectionId);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $sections->count()) {
            return;
        }

        $other = $sections[$target];

        $sectionOrder = $section->order;
        $otherOrder = $other->order;

        $this->page->sections()->where('id', $section->id)->update(['order' => $otherOrder]);
        $this->page->sections()->where('id', $other->id)->update(['order' => $sectionOrder]);
    }

    public function deleteSection(int $sectionId): void
    {
        $this->page->sections()->where('id', $sectionId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.pages.edit', [
            'sections' => $this->page->sections()->with('sectionType')->get(),
            'sectionTypes' => SectionType::query()->orderBy('name')->get(),
        ]);
    }
}
