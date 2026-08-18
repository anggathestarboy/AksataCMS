<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class SectionEdit extends Component
{
    use WithFileUploads;

    public Page $page;

    public Section $section;

    /**
     * @var array<string, array<string, mixed>>
     */
    public array $content = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    public array $uploads = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $fields = [];

    public function mount(Page $page, Section $section): void
    {
        abort_unless($section->page_id === $page->id, 404);

        $this->page = $page;
        $this->section = $section->load('sectionType');
        $this->fields = $section->sectionType->fields ?? [];

        foreach (config('cms.locales') as $locale => $label) {
            $translation = $section->translations->firstWhere('locale', $locale);
            $this->content[$locale] = $translation?->content ?? [];
        }
    }

    public function addRepeaterItem(string $locale, string $path): void
    {
        $items = data_get($this->content, "$locale.$path", []);
        $items[] = $this->emptyItemFor($locale, $path);
        data_set($this->content, "$locale.$path", $items);
    }

    public function removeRepeaterItem(string $locale, string $path, int $index): void
    {
        $items = data_get($this->content, "$locale.$path", []);
        unset($items[$index]);
        data_set($this->content, "$locale.$path", array_values($items));
    }

    public function moveRepeaterItem(string $locale, string $path, int $index, string $direction): void
    {
        $items = data_get($this->content, "$locale.$path", []);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= count($items)) {
            return;
        }

        [$items[$index], $items[$target]] = [$items[$target], $items[$index]];

        data_set($this->content, "$locale.$path", array_values($items));
    }

    /**
     * @return array<string, mixed>
     */
    protected function emptyItemFor(string $locale, string $path): array
    {
        $keys = array_values(array_filter(explode('.', $path), fn (string $segment): bool => ! is_numeric($segment)));

        $definitions = $this->fields;
        $item = [];

        foreach ($keys as $key) {
            $definition = collect($definitions)->firstWhere('key', $key);

            if ($definition === null) {
                $item = [];

                break;
            }

            $definitions = $definition['fields'] ?? [];
            $item = collect($definitions)
                ->mapWithKeys(fn (array $field): array => [$field['key'] => $this->defaultFor($field)])
                ->all();
        }

        return $item;
    }

    protected function defaultFor(array $field): mixed
    {
        return match ($field['type'] ?? 'text') {
            'repeater' => [],
            'link' => [
                'label' => '',
                'link_type' => 'internal',
                'url' => '',
                'page_id' => null,
                'open_in_new_tab' => false,
            ],
            default => '',
        };
    }

    public function save(): void
    {
        $this->validateContent();
        $this->storeUploads();
        $this->syncLinkFields();

        foreach ($this->content as $locale => $content) {
            $this->section->translations()->updateOrCreate(
                ['locale' => $locale],
                ['content' => $content],
            );
        }

        session()->flash('status', 'Section content saved successfully.');
    }

    protected function validateContent(): void
    {
        $errors = [];

        foreach ($this->fields as $field) {
            $this->assertRequired(config('cms.default_locale'), $field, [], $errors);
        }

        if (count($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @param  array<int, string>  $pathSegments
     * @param  array<string, string>  $errors
     */
    protected function assertRequired(string $locale, array $field, array $pathSegments, array &$errors): void
    {
        $statePath = implode('.', array_merge([$locale], $pathSegments, [$field['key']]));
        $value = data_get($this->content, $statePath);

        if (($field['type'] ?? 'text') === 'image' && blank($value)) {
            $value = data_get($this->uploads, $statePath);
        }

        if (($field['required'] ?? false) && blank($value)) {
            $errors["content.{$statePath}"] = "The {$field['label']} field is required in the {$locale} locale.";
        }

        if (($field['type'] ?? 'text') === 'link' && $value !== null) {
            $linkData = (array) $value;
            $linkType = $linkData['link_type'] ?? '';
            $hasUrl = filled($linkData['url'] ?? '');
            $hasPage = filled($linkData['page_id'] ?? null);

            if (($field['required'] ?? false) && ($linkType === '' || (! $hasUrl && ! $hasPage))) {
                $errors["content.{$statePath}"] = "The {$field['label']} link is required in the {$locale} locale.";
            }
        }

        if (($field['type'] ?? 'text') === 'repeater') {
            $items = (array) data_get($this->content, $statePath, []);

            foreach ($items as $index => $item) {
                foreach (($field['fields'] ?? []) as $subField) {
                    $this->assertRequired($locale, $subField, array_merge($pathSegments, [$field['key'], (string) $index]), $errors);
                }
            }
        }
    }

    protected function storeUploads(): void
    {
        $this->walkUploads($this->uploads, $this->content);
        $this->uploads = [];
    }

    private function syncLinkFields(): void
    {
        $defaultLocale = config('cms.default_locale');

        foreach ($this->fields as $field) {
            if (($field['type'] ?? 'text') !== 'link') {
                continue;
            }

            $defaultContent = $this->content[$defaultLocale] ?? [];
            $defaultLink = (array) data_get($defaultContent, $field['key'], []);

            if (empty($defaultLink)) {
                continue;
            }

            $syncData = [
                'link_type' => $defaultLink['link_type'] ?? 'internal',
                'url' => $defaultLink['url'] ?? '',
                'page_id' => $defaultLink['page_id'] ?? null,
                'open_in_new_tab' => $defaultLink['open_in_new_tab'] ?? false,
            ];

            foreach (array_keys(config('cms.locales')) as $locale) {
                if ($locale === $defaultLocale) {
                    continue;
                }

                $otherContent = $this->content[$locale] ?? [];
                $otherLink = (array) data_get($otherContent, $field['key'], []);

                data_set($this->content, "{$locale}.{$field['key']}", array_merge($otherLink, $syncData));
            }
        }
    }

    /**
     * @param  array<string, mixed>  $uploads
     * @param  array<string, mixed>  $content
     */
    protected function walkUploads(array $uploads, array &$content): void
    {
        foreach ($uploads as $key => $value) {
            if ($value instanceof TemporaryUploadedFile) {
                $content[$key] = $value->store('sections', 'public');
            } elseif (is_array($value)) {
                if (! isset($content[$key]) || ! is_array($content[$key])) {
                    $content[$key] = [];
                }

                $this->walkUploads($value, $content[$key]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.pages.section-edit', [
            'sectionType' => $this->section->sectionType,
            'pages' => Page::query()->with('translations')->orderBy('order')->get(),
        ]);
    }
}
