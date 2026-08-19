<?php

namespace App\Services;

use App\Models\SectionType;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class SectionTemplateGenerator
{
    protected function directory(): string
    {
        return resource_path('views/public/sections');
    }

    public function path(string $slug): string
    {
        return $this->directory() . DIRECTORY_SEPARATOR . $slug . '.blade.php';
    }

    public function viewName(string $slug): string
    {
        return 'public.sections.' . $slug;
    }

    public function exists(SectionType|string $slugOrType): bool
    {
        $slug = $slugOrType instanceof SectionType ? $slugOrType->slug : $slugOrType;

        return View::exists($this->viewName($slug));
    }

    public function generate(SectionType $type, bool $force = false): void
    {
        $path = $this->path($type->slug);

        if ($force || ! File::exists($path)) {
            File::ensureDirectoryExists($this->directory());
            File::put($path, $this->defaultTemplate($type));
        }
    }

    public function rename(SectionType $type, string $oldSlug): void
    {
        if ($oldSlug === $type->slug) {
            $this->generate($type);

            return;
        }

        $oldPath = $this->path($oldSlug);

        if (File::exists($oldPath)) {
            File::ensureDirectoryExists($this->directory());
            File::move($oldPath, $this->path($type->slug));
        } else {
            $this->generate($type);
        }
    }

    public function delete(SectionType $type): void
    {
        File::delete($this->path($type->slug));
    }

    protected function defaultTemplate(SectionType $type): string
    {
        $body = $this->buildFieldCalls($type->fields ?? []);

        $name = $type->name;
        $slug = $type->slug;

        return <<<BLADE
{{--
    Template Section: {$name} ({$slug})
    =========================================================
    Variabel: \$sectionType, \$section, \$locale, \$content, \$fields

    Contoh akses:
        @field('key-name')
    =========================================================
--}}
<section>
    {$body}
</section>
BLADE;
    }

    protected function buildFieldCalls(array $fields, string $indent = '    '): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $key = $field['key'];
            $type = $field['type'] ?? 'text';

            if ($type === 'link') {
                $lines[] = "{$indent}<a href=\"@field('{$key}')\" target=\"@field('{$key}_target')\">@field('{$key}_label')</a>";
            } elseif ($type === 'repeater') {
                $subFields = $field['fields'] ?? [];
                $lines[] = "{$indent}@repeater('{$key}')";

                foreach ($subFields as $subField) {
                    $lines[] = "{$indent}    @field('{$subField['key']}')";
                }

                $lines[] = "{$indent}@endrepeater";
            } else {
                $lines[] = "{$indent}@field('{$key}')";
            }
        }

        return implode("\n", $lines);
    }
}
