<?php

namespace App\Livewire\Admin\Navigations;

use Illuminate\Support\Str;
use Livewire\Component;

abstract class Form extends Component
{
    public string $name = '';

    public string $slug = '';

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
            'slug' => ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationMessages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers and hyphens (e.g. "main-navbar").',
        ];
    }
}
