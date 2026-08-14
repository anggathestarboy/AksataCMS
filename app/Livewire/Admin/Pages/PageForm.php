<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

abstract class PageForm extends Component
{
    public string $status = 'draft';

    public ?string $publishedAt = null;

    /**
     * @var array<string, array<string, mixed>>
     */
    public array $translations = [];

    public ?Page $page = null;

    /**
     * @return array<string, string>
     */
    protected function defaultMeta(): array
    {
        return [
            'meta_title' => '',
            'meta_description' => '',
            'og_image' => '',
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function defaultTranslations(): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(fn ($label, $locale): array => [
                $locale => [
                    'title' => '',
                    'slug' => '',
                    'meta' => $this->defaultMeta(),
                ],
            ])
            ->all();
    }

    public function updatedStatus(string $value): void
    {
        $this->resetValidation('publishedAt');
    }

    public function updated(string $name, mixed $value): void
    {
        if (! preg_match('/^translations\.([a-z]{2,3})\.title$/', $name, $matches)) {
            return;
        }

        $slugPath = $matches[1] . '.slug';

        if (blank(data_get($this->translations, $slugPath))) {
            data_set($this->translations, $slugPath, Str::slug((string) $value));
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = [
            'status' => ['required', 'string', Rule::in(['draft', 'published'])],
            'publishedAt' => ['nullable', 'date'],
        ];

        foreach (config('cms.locales') as $locale => $label) {
            if ($locale === config('cms.default_locale')) {
                $rules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
                $rules["translations.{$locale}.slug"] = ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];
            } else {
                $rules["translations.{$locale}.title"] = ['nullable', 'string', 'max:255', 'required_with:translations.' . $locale . '.slug'];
                $rules["translations.{$locale}.slug"] = ['nullable', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'required_with:translations.' . $locale . '.title'];
            }

            $rules["translations.{$locale}.meta.meta_title"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.meta.meta_description"] = ['nullable', 'string', 'max:500'];
            $rules["translations.{$locale}.meta.og_image"] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    protected function assertSlugsAreUnique(): void
    {
        foreach (config('cms.locales') as $locale => $label) {
            $slug = $this->translations[$locale]['slug'] ?? null;

            if (blank($slug)) {
                continue;
            }

            $query = PageTranslation::query()
                ->where('locale', $locale)
                ->where('slug', $slug);

            if ($this->page !== null) {
                $query->where('page_id', '!=', $this->page->getKey());
            }

            if ($query->exists()) {
                $this->addError("translations.{$locale}.slug", "The slug is already used in the {$locale} locale.");
            }
        }
    }

    protected function validateForm(): array
    {
        $validated = $this->validate(null, $this->validationMessages());

        $this->assertSlugsAreUnique();

        if ($this->getErrorBag()->isNotEmpty()) {
            throw ValidationException::withMessages($this->getErrorBag()->toArray());
        }

        return $validated;
    }

    /**
     * @return array<string, string>
     */
    protected function validationMessages(): array
    {
        $messages = [
            'translations.*.slug.regex' => 'The slug may only contain lowercase letters, numbers and hyphens (e.g. "tentang-kami").',
        ];

        foreach (config('cms.locales') as $locale => $label) {
            $messages["translations.{$locale}.title.required"] = "The title is required in the {$label} locale.";
            $messages["translations.{$locale}.title.required_with"] = "The title is required in the {$label} locale.";
            $messages["translations.{$locale}.slug.required"] = "The slug is required in the {$label} locale.";
            $messages["translations.{$locale}.slug.required_with"] = "The slug is required in the {$label} locale.";
        }

        return $messages;
    }

    protected function resolvePublishedAt(): ?Carbon
    {
        if ($this->status === 'published' && blank($this->publishedAt)) {
            return now();
        }

        return filled($this->publishedAt) ? Carbon::parse($this->publishedAt) : null;
    }

    protected function syncTranslations(Page $page): void
    {
        foreach ($this->translations as $locale => $values) {
            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $values['title'] ?? '',
                    'slug' => $values['slug'] ?? '',
                    'meta' => array_replace($this->defaultMeta(), $values['meta'] ?? []),
                ],
            );
        }
    }
}
