<?php

namespace App\Services;

use Illuminate\Support\Str;

class DynamicFormBuilder
{
    public const TYPES = ['text', 'textarea', 'rich-text', 'image', 'repeater'];

    /**
     * Normalize a SectionType fields definition into a plain, render-safe structure.
     *
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, array<string, mixed>>
     */
    public static function build(array $fields): array
    {
        return array_map(
            static fn (array $field): array => self::normalize($field),
            array_values($fields),
        );
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    protected static function normalize(array $field): array
    {
        $key = (string) ($field['key'] ?? 'field');
        $type = in_array($field['type'] ?? 'text', self::TYPES, true) ? $field['type'] : 'text';

        $normalized = [
            'key' => $key,
            'label' => (string) ($field['label'] ?? Str::headline($key)),
            'type' => $type,
            'required' => (bool) ($field['required'] ?? false),
        ];

        if ($type === 'repeater') {
            $normalized['fields'] = self::build($field['fields'] ?? []);
        }

        return $normalized;
    }
}
