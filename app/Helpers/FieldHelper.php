<?php

namespace App\Helpers;

use App\Models\Media;
use App\Models\Page;
use Illuminate\Support\Facades\Storage;

class FieldHelper
{
    private const IMAGE_PROPERTY_PATTERN = '/^(.+?)_(width|height|alt|loading)$/';

    public static function render(array $content, array $fields, string $key, mixed $source = null): string
    {
        $source ??= $content;

        if (preg_match(self::IMAGE_PROPERTY_PATTERN, $key, $m)) {
            $baseKey = $m[1];
            $property = $m[2];

            $field = collect($fields)->firstWhere('key', $baseKey);

            if (($field['type'] ?? 'text') === 'image') {
                $value = data_get($source, $baseKey, '');

                return self::resolveImageProperty($value, $property);
            }
        }

        $subKey = null;

        if (preg_match('/^(.+?)_(label|url|target)$/', $key, $m)) {
            $key = $m[1];
            $subKey = $m[2];
        }

        $value = data_get($source, $key, '');
        $field = collect($fields)->firstWhere('key', $key);
        $type = $field['type'] ?? 'text';

        if ($type === 'link' && is_array($value)) {
            if ($subKey === 'url') {
                return self::resolveLinkUrl($value);
            }

            if ($subKey === 'target') {
                return ($value['open_in_new_tab'] ?? false) ? '_blank' : '';
            }

            if ($subKey === 'label') {
                return (string) ($value['label'] ?? '');
            }

            return self::resolveLinkUrl($value);
        }

        if ($type === 'image') {
            if ($subKey !== null) {
                return self::resolveImageProperty($value, $subKey);
            }

            return self::resolveImageUrl($value);
        }

        if ($type === 'rich-text' && ! blank($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            if (isset($value['link_type']) || isset($value['url'])) {
                return self::resolveLinkUrl($value);
            }

            return '';
        }

        return e($value);
    }

    private static function resolveImageProperty(mixed $value, string $property): string
    {
        if (is_array($value)) {
            return (string) ($value[$property] ?? '');
        }

        if (is_string($value) && $value !== '') {
            $metadata = Media::resolveMetadataFromPath($value);

            return (string) ($metadata[$property] ?? '');
        }

        return '';
    }

    private static function resolveImageUrl(mixed $value): string
    {
        if (is_array($value)) {
            $path = $value['path'] ?? '';

            return blank($path) ? '' : Storage::disk('public')->url($path);
        }

        if (blank($value)) {
            return '';
        }

        return str_starts_with((string) $value, 'http')
            ? (string) $value
            : Storage::disk('public')->url((string) $value);
    }

    private static function resolveLinkUrl(array $link): string
    {
        $linkType = $link['link_type'] ?? 'internal';

        if ($linkType === 'page' && ! empty($link['page_id'])) {
            $page = Page::with('translations')->find($link['page_id']);

            return $page?->url() ?? '#';
        }

        return $link['url'] ?? '#';
    }
}
