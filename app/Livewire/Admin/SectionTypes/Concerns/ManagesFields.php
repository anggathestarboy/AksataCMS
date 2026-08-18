<?php

namespace App\Livewire\Admin\SectionTypes\Concerns;

use Illuminate\Support\Str;

trait ManagesFields
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $fields = [];

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fieldsAt(string $path): array
    {
        if ($path === '') {
            return $this->fields;
        }

        return (array) data_get($this->fields, $path, []);
    }

    protected function writeFieldsAt(string $path, array $items): void
    {
        if ($path === '') {
            $this->fields = $items;

            return;
        }

        data_set($this->fields, $path, $items);
    }

    public function addField(?string $path = null): void
    {
        $path ??= '';

        $items = $this->fieldsAt($path);
        $items[] = [
            'key' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'fields' => [],
        ];

        $this->writeFieldsAt($path, $items);
    }

    public function updated(string $name, mixed $value): void
    {
        if (! preg_match('/^fields\.\d+(?:\.fields\.\d+)*\.label$/', $name)) {
            return;
        }

        $keyPath = (string) preg_replace(['/^fields\./', '/\.label$/'], ['', '.key'], $name);

        if (blank(data_get($this->fields, $keyPath))) {
            data_set($this->fields, $keyPath, Str::slug((string) $value));
        }
    }

    /**
     * Split a field path like "0.fields.1" into its parent path and index.
     * Top-level paths ("0", "1") map to an empty parent path.
     *
     * @return array{0: string, 1: int}
     */
    protected function parseFieldPath(string $path): array
    {
        if (str_contains($path, '.')) {
            return [(string) Str::beforeLast($path, '.'), (int) Str::afterLast($path, '.')];
        }

        return ['', (int) $path];
    }

    public function removeField(string $path): void
    {
        [$parentPath, $index] = $this->parseFieldPath($path);

        $items = $this->fieldsAt($parentPath);
        unset($items[$index]);

        $this->writeFieldsAt($parentPath, array_values($items));
    }

    public function moveField(string $path, string $direction): void
    {
        [$parentPath, $index] = $this->parseFieldPath($path);

        $items = $this->fieldsAt($parentPath);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= count($items)) {
            return;
        }

        [$items[$index], $items[$target]] = [$items[$target], $items[$index]];

        $this->writeFieldsAt($parentPath, array_values($items));
    }

    /**
     * @return array<string, mixed>
     */
    protected function normalizeFields(): array
    {
        return \App\Services\DynamicFormBuilder::build($this->fields);
    }
}
