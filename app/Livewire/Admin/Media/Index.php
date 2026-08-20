<?php

namespace App\Livewire\Admin\Media;

use App\Models\Folder;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public ?int $currentFolderId = null;

    /** @var array<int> */
    public array $selectedIds = [];

    /** @var array{action: string, ids: int[]} | null */
    public ?array $clipboard = null;

    public ?string $editingId = null;

    public string $editName = '';

    public string $editAltText = '';

    public string $newFolderName = '';

    public ?int $renamingFolderId = null;

    public string $renameFolderName = '';

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

        Media::create([
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
        $this->dispatch('show-toast', message: 'Media uploaded successfully.');
    }

    public function navigateToFolder(?int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->selectedIds = [];
        $this->resetPage();
    }

    public function createFolder(): void
    {
        $this->validate([
            'newFolderName' => ['required', 'string', 'max:255'],
        ]);

        Folder::create([
            'name' => $this->newFolderName,
            'parent_id' => $this->currentFolderId,
        ]);

        $this->newFolderName = '';
        $this->dispatch('show-toast', message: 'Folder created.');
    }

    public function startRenameFolder(int $folderId): void
    {
        $folder = Folder::findOrFail($folderId);
        $this->renamingFolderId = $folderId;
        $this->renameFolderName = $folder->name;
    }

    public function saveRenameFolder(): void
    {
        $this->validate([
            'renameFolderName' => ['required', 'string', 'max:255'],
            'renamingFolderId' => ['required', 'integer', 'exists:folders,id'],
        ]);

        Folder::findOrFail($this->renamingFolderId)->update([
            'name' => $this->renameFolderName,
        ]);

        $this->renamingFolderId = null;
        $this->renameFolderName = '';
        $this->dispatch('show-toast', message: 'Folder renamed.');
    }

    public function cancelRenameFolder(): void
    {
        $this->renamingFolderId = null;
        $this->renameFolderName = '';
    }

    public function deleteFolder(int $folderId): void
    {
        $folder = Folder::findOrFail($folderId);

        Media::where('folder_id', $folderId)->update(['folder_id' => $folder->parent_id]);

        $folder->delete();

        if ($this->currentFolderId === $folderId) {
            $this->currentFolderId = null;
        }

        $this->dispatch('show-toast', message: 'Folder deleted.');
    }

    public function toggleSelect(int $id): void
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds[] = $id;
        }
    }

    public function selectAll(): void
    {
        $items = $this->getMediaQuery()->pluck('id')->toArray();
        $this->selectedIds = $items;
    }

    public function deselectAll(): void
    {
        $this->selectedIds = [];
    }

    public function cutSelected(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $this->clipboard = [
            'action' => 'cut',
            'ids' => $this->selectedIds,
        ];
        $this->selectedIds = [];
        $this->dispatch('show-toast', message: 'Items cut to clipboard.');
    }

    public function copySelected(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $this->clipboard = [
            'action' => 'copy',
            'ids' => $this->selectedIds,
        ];
        $this->selectedIds = [];
        $this->dispatch('show-toast', message: 'Items copied to clipboard.');
    }

    public function paste(): void
    {
        if (! $this->clipboard) {
            return;
        }

        $ids = $this->clipboard['ids'];
        $action = $this->clipboard['action'];

        foreach ($ids as $id) {
            $media = Media::find($id);

            if (! $media) {
                continue;
            }

            if ($action === 'cut') {
                $media->update(['folder_id' => $this->currentFolderId]);
            } else {
                $newPath = $this->copyFile($media->path);
                $newSlug = $media->slug ? $media->slug.'-copy' : null;

                if ($newSlug) {
                    $originalSlug = $newSlug;
                    $counter = 1;
                    while (Media::where('slug', $newSlug)->exists()) {
                        $newSlug = $originalSlug.'-'.$counter;
                        $counter++;
                    }
                }

                Media::create([
                    'name' => $media->name.' (copy)',
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'path' => $newPath,
                    'alt_text' => $media->alt_text,
                    'created_by' => auth()->id(),
                    'width' => $media->width,
                    'height' => $media->height,
                    'loading' => $media->loading,
                    'slug' => $newSlug,
                    'folder_id' => $this->currentFolderId,
                ]);
            }
        }

        Media::clearPathCache();

        $this->clipboard = null;
        $actionLabel = $action === 'cut' ? 'moved' : 'copied';
        $this->dispatch('show-toast', message: "Items {$actionLabel} successfully.");
    }

    public function clearClipboard(): void
    {
        $this->clipboard = null;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        foreach ($this->selectedIds as $id) {
            $media = Media::find($id);

            if ($media) {
                if (Storage::disk('public')->exists($media->path)) {
                    Storage::disk('public')->delete($media->path);
                }
                $media->delete();
            }
        }

        Media::clearPathCache();

        $this->selectedIds = [];
        $this->dispatch('show-toast', message: 'Selected media deleted.');
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

        Media::clearPathCache();

        $this->dispatch('show-toast', message: 'Media deleted successfully.');
    }

    private function getMediaQuery(): Builder
    {
        return Media::query()
            ->inFolder($this->currentFolderId)
            ->search($this->search);
    }

    private function copyFile(string $path): string
    {
        $disk = Storage::disk('public');
        $contents = $disk->get($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $newName = Str::random(40).'.'.$extension;
        $newPath = $directory.'/'.$newName;

        $disk->put($newPath, $contents);

        return $newPath;
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

        return view('livewire.admin.media.index', [
            'media' => $this->getMediaQuery()
                ->orderByDesc('created_at')
                ->paginate(24),
            'folders' => Folder::query()
                ->where('parent_id', $this->currentFolderId)
                ->orderBy('name')
                ->get(),
            'ancestors' => $ancestors,
            'currentFolder' => $this->currentFolderId ? Folder::find($this->currentFolderId) : null,
        ]);
    }
}
