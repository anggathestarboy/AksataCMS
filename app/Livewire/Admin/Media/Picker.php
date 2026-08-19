<?php

namespace App\Livewire\Admin\Media;

use App\Models\Media;
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

    public $upload;

    public function openPicker(?string $fieldPath = null, ?string $sectionId = null): void
    {
        $this->open = true;
        $this->targetFieldPath = $fieldPath;
        $this->targetSectionId = $sectionId;
        $this->search = '';
        $this->resetPage();
    }

    public function closePicker(): void
    {
        $this->open = false;
        $this->targetFieldPath = null;
        $this->targetSectionId = null;
    }

    public function select(int $id): void
    {
        $media = Media::find($id);

        if (! $media) {
            return;
        }

        $this->dispatch('media-selected', [
            'path' => $media->path,
            'fieldPath' => $this->targetFieldPath,
            'sectionId' => $this->targetSectionId,
            'url' => $media->url,
        ]);

        $this->closePicker();
    }

    public function updatedUpload(): void
    {
        $this->validate([
            'upload' => ['required', 'image', 'max:5120'],
        ]);

        $file = $this->upload;
        $path = $file->store('media', 'public');

        $media = Media::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'created_by' => auth()->id(),
        ]);

        $this->upload = null;

        $this->dispatch('media-selected', [
            'path' => $media->path,
            'fieldPath' => $this->targetFieldPath,
            'sectionId' => $this->targetSectionId,
            'url' => $media->url,
        ]);

        $this->closePicker();
    }

    public function render()
    {
        return view('livewire.admin.media.picker', [
            'items' => Media::query()
                ->search($this->search)
                ->orderByDesc('created_at')
                ->paginate(12),
        ]);
    }
}
