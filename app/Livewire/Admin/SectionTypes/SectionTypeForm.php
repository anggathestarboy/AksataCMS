<?php

namespace App\Livewire\Admin\SectionTypes;

use App\Livewire\Admin\SectionTypes\Concerns\ManagesFields;
use App\Services\DynamicFormBuilder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

abstract class SectionTypeForm extends Component
{
    use ManagesFields;

    public string $name = '';

    public string $slug = '';

    public string $icon = '';

    /**
     * @return array<int, string>
     */
    public function availableIcons(): array
    {
        return ['document', 'sparkles', 'star', 'chat', 'image', 'list', 'quote', 'video', 'map', 'users', 'globe', 'calendar', 'gift', 'heart'];
    }

    public function updatedName(string $value): void
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', Rule::in($this->availableIcons())],
            'fields' => ['array'],
            'fields.*.key' => ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', 'string', Rule::in(DynamicFormBuilder::TYPES)],
            'fields.*.fields' => ['sometimes', 'array'],
            'fields.*.fields.*.key' => ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'fields.*.fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.fields.*.type' => ['required', 'string', Rule::in(DynamicFormBuilder::TYPES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationMessages(): array
    {
        return [
            'fields.*.key.regex' => 'The key may only contain lowercase letters, numbers and hyphens (e.g. "heading-text").',
            'fields.*.fields.*.key.regex' => 'The key may only contain lowercase letters, numbers and hyphens (e.g. "heading-text").',
        ];
    }
}
