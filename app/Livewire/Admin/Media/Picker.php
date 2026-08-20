<?php

namespace App\Livewire\Admin\Media;

use App\Models\Folder;
use App\Models\Media;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Picker extends Component
{
    use WithFileUploads, WithPagination;

    public bool $open = false;

    public string $search = '';

    public ?string $targetFieldPath = null;

    public ?string $targetSectionId = null;

    public ?int $currentFolderId = null;

    public $upload;

    public function openPicker(?string $fieldPath = null, ?string $sectionId = null): void
    {
        $this->open = true;
        $this->targetFieldPath = $fieldPath;
        $this->targetSectionId = $sectionId;
        $this->search = '';
        $this->currentFolderId = null;
        $this->resetPage();
    }

    public function closePicker(): void
    {
        $this->open = false;
        $this->targetFieldPath = null;
        $this->targetSectionId = null;
        $this->currentFolderId = null;
    }

    public function navigateToFolder(?int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->resetPage();
    }

    public function select(int $id): void
    {
        $this->closePicker();
    }

    public function updatedUpload(): void
    {
        $this->validate([
            'upload' => ['required', 'image', 'max:5120'],
        ]);

        $file = $this->upload;
        $path = $file->store('media', 'public');

        $slug = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $originalSlug = $slug;
        $counter = 1;
        while (Media::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        $dimensions = null;
        if (method_exists($file, 'getPathname')) {
            $dimensions = @getimagesize($file->getPathname());
        }

        $media = Media::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'alt_text' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'created_by' => auth()->id(),
            'width' => $dimensions ? $dimensions[0] : null,
            'height' => $dimensions ? $dimensions[1] : null,
            'loading' => 'lazy',
            'slug' => $slug,
            'folder_id' => $this->currentFolderId,
        ]);

        Media::clearPathCache();

        $this->upload = null;

        $this->dispatch('media-selected', [
            'path' => $media->path,
            'fieldPath' => $this->targetFieldPath,
            'sectionId' => $this->targetSectionId,
            'url' => $media->url,
            'width' => $media->width,
            'height' => $media->height,
            'alt' => $media->alt_text ?? $media->name,
            'loading' => $media->loading ?? 'lazy',
        ]);

        $this->closePicker();
    }

    public function render()
    {
        $ancestors = collect();
        if ($this->currentFolderId !== null) {
            $folder = Folder::find($this->currentFolderId);
            if ($folder) {
                $ancestors = $folder->ancestors();
            }
        }

        return view('livewire.admin.media.picker', [
            'items' => Media::query()
                ->inFolder($this->currentFolderId)
                ->search($this->search)
                ->orderByDesc('created_at')
                ->paginate(12),
            'folders' => Folder::query()
                ->where('parent_id', $this->currentFolderId)
                ->orderBy('name')
                ->get(),
            'ancestors' => $ancestors,
            'currentFolder' => $this->currentFolderId ? Folder::find($this->currentFolderId) : null,
        ]);
    }
}
