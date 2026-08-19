<?php

namespace App\Helpers;

use App\Models\Page;
use Illuminate\Support\Facades\Storage;

class FieldHelper
{
    public static function render(array $content, array $fields, string $key, mixed $source = null): string
    {
        $source ??= $content;
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

        if ($type === 'image' && ! blank($value)) {
            return str_starts_with((string) $value, 'http')
                ? $value
                : Storage::disk('public')->url($value);
        }

        if ($type === 'rich-text' && ! blank($value)) {
            return (string) $value;
        }

        return e($value);
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
