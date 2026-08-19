<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FieldHelper
{
    public static function render(array $content, array $fields, string $key, mixed $source = null): string
    {
        $source ??= $content;
        $value = data_get($source, $key, '');
        $field = collect($fields)->firstWhere('key', $key);
        $type = $field['type'] ?? 'text';

        if ($type === 'image' && ! blank($value)) {
            return str_starts_with((string) $value, 'http')
                ? $value
                : Storage::disk('public')->url($value);
        }

        if ($type === 'rich-text' && ! blank($value)) {
            return (string) $value;
        }

        if ($type === 'link' && is_array($value)) {
            return e($value['label'] ?? '');
        }

        return e($value);
    }
}
