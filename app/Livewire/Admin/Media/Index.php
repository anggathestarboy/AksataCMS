<?php

namespace App\Livewire\Admin\Media;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public ?string $editingId = null;

    public string $editName = '';

    public string $editAltText = '';

    public $upload;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedUpload(): void
    {
        $this->validate([
            'upload' => ['required', 'image', 'max:5120'],
        ]);

        $file = $this->upload;
        $path = $file->store('media', 'public');

        Media::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'created_by' => auth()->id(),
        ]);

        $this->upload = null;
        $this->dispatch('show-toast', message: 'Media uploaded successfully.');
    }

    public function startEdit(int $id): void
    {
        $media = Media::findOrFail($id);
        $this->editingId = (string) $id;
        $this->editName = $media->name;
        $this->editAltText = $media->alt_text ?? '';
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editAltText' => ['nullable', 'string', 'max:255'],
        ]);

        Media::findOrFail($this->editingId)->update([
            'name' => $this->editName,
            'alt_text' => $this->editAltText,
        ]);

        $this->editingId = null;
        $this->dispatch('show-toast', message: 'Media updated successfully.');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function delete(int $id): void
    {
        $media = Media::findOrFail($id);

        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        $this->dispatch('show-toast', message: 'Media deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.media.index', [
            'media' => Media::query()
                ->search($this->search)
                ->orderByDesc('created_at')
                ->paginate(24),
        ]);
    }
}
