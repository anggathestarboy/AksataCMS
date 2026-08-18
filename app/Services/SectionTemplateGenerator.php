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
        $template = <<<'BLADE'
{{--
    Template Section: __TYPE_NAME__ (__TYPE_SLUG__)
    =========================================================
    File ini digenerate otomatis saat Section Type dibuat.
    Edit bebas untuk menyesuaikan tampilan section di halaman publik.

    Variabel yang tersedia:
        $sectionType  => App\Models\SectionType
        $section      => App\Models\Section
        $locale       => string, kode locale aktif (mis. "id" atau "en")
        $content      => array, isi konten untuk locale aktif
        $fields       => array, definisi field dari Section Type

    Contoh akses nilai field:
        {{ $content['heading'] ?? '' }}
    =========================================================
--}}
<section class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
    @foreach ($fields as $field)
        @php
            $key = $field['key'];
            $type = $field['type'] ?? 'text';
            $value = data_get($content, $key);
        @endphp

        @if ($type === 'image')
            @if (! blank($value))
                @php
                    $imageUrl = str_starts_with((string) $value, 'http')
                        ? $value
                        : \Illuminate\Support\Facades\Storage::disk('public')->url($value);
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $field['label'] }}" class="w-full max-w-full h-auto rounded-lg my-3">
            @endif
        @elseif ($type === 'repeater')
            @if (! blank($value))
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ((array) $value as $item)
                        <div class="rounded-lg border border-gray-200 p-4">
                            @include('public.partials.dynamic-fields', [
                                'fields' => $field['fields'] ?? [],
                                'content' => (array) $item,
                                'path' => '',
                            ])
                        </div>
                    @endforeach
                </div>
            @endif
        @elseif ($type === 'rich-text')
            @if (! blank($value))
                <div class="prose prose-slate max-w-none my-3">{!! $value !!}</div>
            @endif
        @else
            @if (! blank($value) && ! is_array($value))
                @if (str_contains($key, 'heading') || str_contains($key, 'title'))
                    <h2 class="mt-6 mb-3 text-2xl font-bold text-gray-900">{{ $value }}</h2>
                @elseif (str_contains($key, 'subtitle') || str_contains($key, 'subheading'))
                    <h3 class="mt-4 mb-2 text-xl font-semibold text-gray-800">{{ $value }}</h3>
                @elseif ($type === 'textarea')
                    <p class="my-2 text-gray-700 whitespace-pre-line">{{ $value }}</p>
                @else
                    <p class="my-2 text-gray-700">{{ $value }}</p>
                @endif
            @endif
        @endif
    @endforeach
</section>
BLADE;

        return str_replace(
            ['__TYPE_NAME__', '__TYPE_SLUG__'],
            [$type->name, $type->slug],
            $template
        );
    }
}
