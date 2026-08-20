<?php

namespace App\Livewire\Admin\Media;

use App\Models\Folder;
use App\Models\Media;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Edit extends Component
{
    public int $mediaId;

    public string $editAltText = '';

    public string $editLoading = 'lazy';

    public ?int $editFolderId = null;

    public function mount(int $id): void
    {
        $this->mediaId = $id;
        $media = Media::findOrFail($id);
        $this->editAltText = $media->alt_text ?? $media->name;
        $this->editLoading = $media->loading ?? 'lazy';
        $this->editFolderId = $media->folder_id;
    }

    public function save(): void
    {
        $this->validate([
            'editAltText' => ['required', 'string', 'max:255'],
            'editLoading' => ['required', 'string', 'in:lazy,eager'],
            'editFolderId' => ['nullable', 'integer', 'exists:folders,id'],
        ]);

        Media::findOrFail($this->mediaId)->update([
            'alt_text' => $this->editAltText,
            'loading' => $this->editLoading,
            'folder_id' => $this->editFolderId,
        ]);

        $this->dispatch('show-toast', message: 'Media updated successfully.');
    }

    public function reReadDimensions(): void
    {
        $media = Media::findOrFail($this->mediaId);
        $dims = $media->readDimensionsFromExistingFile();

        if ($dims) {
            $media->update([
                'width' => $dims['width'],
                'height' => $dims['height'],
            ]);
            $this->dispatch('show-toast', message: 'Dimensions updated.');
        } else {
            $this->dispatch('show-toast', message: 'Could not read dimensions.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.admin.media.edit', [
            'media' => Media::findOrFail($this->mediaId),
            'folders' => Folder::orderBy('name')->get(),
        ]);
    }
}
