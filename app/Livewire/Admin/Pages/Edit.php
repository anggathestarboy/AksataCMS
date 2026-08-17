<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Models\Section;
use App\Models\SectionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Edit extends PageForm
{
    use WithFileUploads;

    public ?int $newSectionTypeId = null;

    public ?int $expandedSectionId = null;

    /**
     * @var array<int, array<string, array<string, mixed>>>
     */
    public array $sectionContent = [];

    /**
     * @var array<string, mixed>
     */
    public array $sectionUploads = [];

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

        $this->loadSectionContent();
    }

    private function loadSectionContent(): void
    {
        $this->sectionContent = [];

        foreach ($this->page->sections()->with('sectionType')->get() as $section) {
            foreach (config('cms.locales') as $locale => $label) {
                $translation = $section->translations->firstWhere('locale', $locale);
                $this->sectionContent[$section->id][$locale] = $translation?->content ?? [];
            }
        }
    }

    private function reloadTranslations(): void
    {
        $this->page->refresh();
        $this->translations = $this->defaultTranslations();

        foreach ($this->page->translations as $translation) {
            $this->translations[$translation->locale] = [
                'title' => $translation->title,
                'slug' => $translation->slug,
                'meta' => array_replace($this->defaultMeta(), $translation->meta ?? []),
            ];
        }
    }

    public function toggleSection(int $sectionId): void
    {
        $this->expandedSectionId = $this->expandedSectionId === $sectionId ? null : $sectionId;
    }

    public function save()
    {
        $this->validateForm();
        $this->validateAllSectionContent();

        DB::transaction(function (): void {
            $this->page->update([
                'status' => $this->status,
                'published_at' => $this->resolvePublishedAt(),
            ]);

            $this->syncTranslations($this->page);
            $this->saveAllSectionContent();
        });

        $this->reloadTranslations();

        $this->dispatch('show-toast', message: 'Page updated successfully.');
    }

    private function validateAllSectionContent(): void
    {
        $errors = [];

        foreach ($this->page->sections()->with('sectionType')->get() as $section) {
            $fields = $section->sectionType->fields ?? [];
            $sectionId = $section->id;

            foreach ($fields as $field) {
                $this->assertRequired($this->activeLocale, $field, [], $errors, $sectionId);
            }
        }

        if (count($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function saveAllSectionContent(): void
    {
        foreach ($this->page->sections()->get() as $section) {
            $sectionId = $section->id;

            $this->walkSectionUploads($this->sectionUploads[$sectionId] ?? [], $this->sectionContent[$sectionId], $sectionId);

            $localeContent = $this->sectionContent[$sectionId][$this->activeLocale] ?? [];

            $section->translations()->updateOrCreate(
                ['locale' => $this->activeLocale],
                ['content' => $localeContent],
            );
        }

        $this->sectionUploads = [];
    }

    /**
     * @param  array<int, string>  $pathSegments
     * @param  array<string, string>  $errors
     */
    protected function assertRequired(string $locale, array $field, array $pathSegments, array &$errors, int $sectionId): void
    {
        $statePath = implode('.', array_merge([$locale], $pathSegments, [$field['key']]));
        $value = data_get($this->sectionContent[$sectionId] ?? [], $statePath);

        if (($field['type'] ?? 'text') === 'image' && blank($value)) {
            $uploadKey = "{$sectionId}.{$statePath}";
            $value = data_get($this->sectionUploads, $uploadKey);
        }

        if (($field['required'] ?? false) && blank($value)) {
            $errors["sectionContent.{$sectionId}.{$statePath}"] = "The {$field['label']} field is required in the {$locale} locale.";
        }

        if (($field['type'] ?? 'text') === 'repeater') {
            $items = (array) data_get($this->sectionContent[$sectionId] ?? [], $statePath, []);

            foreach ($items as $index => $item) {
                foreach (($field['fields'] ?? []) as $subField) {
                    $this->assertRequired($locale, $subField, array_merge($pathSegments, [$field['key'], (string) $index]), $errors, $sectionId);
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $uploads
     * @param  array<string, mixed>  $content
     */
    protected function walkSectionUploads(array $uploads, array &$content, int $sectionId): void
    {
        foreach ($uploads as $key => $value) {
            if ($value instanceof TemporaryUploadedFile) {
                $content[$key] = $value->store('sections', 'public');
            } elseif (is_array($value)) {
                if (! isset($content[$key]) || ! is_array($content[$key])) {
                    $content[$key] = [];
                }

                $this->walkSectionUploads($value, $content[$key], $sectionId);
            }
        }
    }

    public function addRepeaterItem(int $sectionId, string $locale, string $path): void
    {
        $items = data_get($this->sectionContent[$sectionId], "$locale.$path", []);
        $items[] = $this->emptyItemFor($sectionId, $locale, $path);
        data_set($this->sectionContent[$sectionId], "$locale.$path", $items);
    }

    public function removeRepeaterItem(int $sectionId, string $locale, string $path, int $index): void
    {
        $items = data_get($this->sectionContent[$sectionId], "$locale.$path", []);
        unset($items[$index]);
        data_set($this->sectionContent[$sectionId], "$locale.$path", array_values($items));
    }

    public function moveRepeaterItem(int $sectionId, string $locale, string $path, int $index, string $direction): void
    {
        $items = data_get($this->sectionContent[$sectionId], "$locale.$path", []);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= count($items)) {
            return;
        }

        [$items[$index], $items[$target]] = [$items[$target], $items[$index]];

        data_set($this->sectionContent[$sectionId], "$locale.$path", array_values($items));
    }

    /**
     * @return array<string, mixed>
     */
    protected function emptyItemFor(int $sectionId, string $locale, string $path): array
    {
        $section = Section::with('sectionType')->find($sectionId);
        $fields = $section?->sectionType->fields ?? [];

        $keys = array_values(array_filter(explode('.', $path), fn (string $segment): bool => ! is_numeric($segment)));

        $definitions = $fields;
        $item = [];

        foreach ($keys as $key) {
            $definition = collect($definitions)->firstWhere('key', $key);

            if ($definition === null) {
                $item = [];

                break;
            }

            $definitions = $definition['fields'] ?? [];
            $item = collect($definitions)
                ->mapWithKeys(fn (array $field): array => [$field['key'] => $this->defaultFieldValue($field)])
                ->all();
        }

        return $item;
    }

    protected function defaultFieldValue(array $field): mixed
    {
        return ($field['type'] ?? 'text') === 'repeater' ? [] : '';
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

        $this->loadSectionContent();
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
        unset($this->sectionContent[$sectionId]);

        if ($this->expandedSectionId === $sectionId) {
            $this->expandedSectionId = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.pages.edit', [
            'sections' => $this->page->sections()->with('sectionType')->get(),
            'sectionTypes' => SectionType::query()->orderBy('name')->get(),
        ]);
    }
}
