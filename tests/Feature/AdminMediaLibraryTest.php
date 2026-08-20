<?php

namespace Tests\Feature;

use App\Livewire\Admin\Media\Edit;
use App\Livewire\Admin\Media\Index;
use App\Models\Folder;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminMediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
    }

    public function test_media_index_page_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->assertOk();
    }

    public function test_upload_generates_unique_slug(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('upload', $file);

        $media = Media::first();
        $this->assertNotNull($media->slug);
    }

    public function test_media_edit_page_renders(): void
    {
        $media = Media::create([
            'name' => 'Test Image',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'path' => 'media/test.webp',
            'width' => 800,
            'height' => 600,
            'loading' => 'lazy',
            'slug' => 'test-image',
        ]);

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['id' => $media->id])
            ->assertOk()
            ->assertSet('editAltText', 'Test Image')
            ->assertSet('editLoading', 'lazy');
    }

    public function test_media_edit_updates_alt_and_loading(): void
    {
        $media = Media::create([
            'name' => 'Test Image',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'path' => 'media/test.webp',
            'width' => 800,
            'height' => 600,
            'loading' => 'lazy',
            'slug' => 'test-image',
        ]);

        Livewire::actingAs($this->user)
            ->test(Edit::class, ['id' => $media->id])
            ->set('editAltText', 'New alt text')
            ->set('editLoading', 'eager')
            ->call('save');

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'alt_text' => 'New alt text',
            'loading' => 'eager',
        ]);
    }

    public function test_creating_folder(): void
    {
        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('newFolderName', 'Images')
            ->call('createFolder');

        $this->assertDatabaseHas('folders', ['name' => 'Images']);
    }

    public function test_navigating_to_folder(): void
    {
        $folder = Folder::create(['name' => 'Subfolder']);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('navigateToFolder', $folder->id)
            ->assertSet('currentFolderId', $folder->id);
    }

    public function test_renaming_folder(): void
    {
        $folder = Folder::create(['name' => 'Old Name']);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('startRenameFolder', $folder->id)
            ->assertSet('renamingFolderId', $folder->id)
            ->set('renameFolderName', 'New Name')
            ->call('saveRenameFolder');

        $this->assertDatabaseHas('folders', ['id' => $folder->id, 'name' => 'New Name']);
    }

    public function test_deleting_folder_moves_files_to_parent(): void
    {
        $parent = Folder::create(['name' => 'Parent']);
        $child = Folder::create(['name' => 'Child', 'parent_id' => $parent->id]);
        $media = Media::create([
            'name' => 'Test',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'path' => 'media/test.webp',
            'folder_id' => $child->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->call('deleteFolder', $child->id);

        $this->assertDatabaseMissing('folders', ['id' => $child->id]);
        $this->assertDatabaseHas('media', ['id' => $media->id, 'folder_id' => $parent->id]);
    }

    public function test_upload_assigns_to_current_folder(): void
    {
        Storage::fake('public');

        $folder = Folder::create(['name' => 'My Folder']);

        $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('currentFolderId', $folder->id)
            ->set('upload', $file);

        $this->assertDatabaseHas('media', [
            'name' => 'photo',
            'folder_id' => $folder->id,
        ]);
    }

    public function test_bulk_select_and_delete(): void
    {
        $media1 = Media::create([
            'name' => 'Img1', 'file_name' => 'a.jpg', 'mime_type' => 'image/jpeg',
            'size' => 100, 'path' => 'media/a.webp',
        ]);
        $media2 = Media::create([
            'name' => 'Img2', 'file_name' => 'b.jpg', 'mime_type' => 'image/jpeg',
            'size' => 100, 'path' => 'media/b.webp',
        ]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('selectedIds', [$media1->id, $media2->id])
            ->call('bulkDelete');

        $this->assertDatabaseMissing('media', ['id' => $media1->id]);
        $this->assertDatabaseMissing('media', ['id' => $media2->id]);
    }

    public function test_copy_and_paste_media(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/original.webp', 'fake-content');

        $media = Media::create([
            'name' => 'Original',
            'file_name' => 'original.webp',
            'mime_type' => 'image/webp',
            'size' => 100,
            'path' => 'media/original.webp',
            'width' => 800,
            'height' => 600,
            'alt_text' => 'My alt',
            'loading' => 'eager',
            'slug' => 'original',
        ]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('selectedIds', [$media->id])
            ->call('copySelected')
            ->assertSet('clipboard.action', 'copy')
            ->call('paste');

        $copies = Media::where('name', 'like', '%Original%')->get();
        $this->assertCount(2, $copies);

        $copy = $copies->firstWhere('id', '!=', $media->id);
        $this->assertNotNull($copy);
        $this->assertEquals($media->width, $copy->width);
        $this->assertEquals($media->height, $copy->height);
        $this->assertEquals($media->alt_text, $copy->alt_text);
        $this->assertEquals($media->loading, $copy->loading);
    }

    public function test_cut_and_paste_moves_media_to_folder(): void
    {
        $folder = Folder::create(['name' => 'Target']);
        $media = Media::create([
            'name' => 'Moved',
            'file_name' => 'moved.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'path' => 'media/moved.webp',
            'folder_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(Index::class)
            ->set('selectedIds', [$media->id])
            ->call('cutSelected')
            ->call('navigateToFolder', $folder->id)
            ->call('paste');

        $this->assertDatabaseHas('media', ['id' => $media->id, 'folder_id' => $folder->id]);
    }

    public function test_folder_prevents_circular_relationship(): void
    {
        $parent = Folder::create(['name' => 'Parent']);
        $child = Folder::create(['name' => 'Child', 'parent_id' => $parent->id]);

        $this->assertTrue($child->isChildOf($parent->id));
        $this->assertFalse($parent->isChildOf($child->id));
    }

    public function test_folder_ancestors(): void
    {
        $root = Folder::create(['name' => 'Root']);
        $mid = Folder::create(['name' => 'Mid', 'parent_id' => $root->id]);
        $leaf = Folder::create(['name' => 'Leaf', 'parent_id' => $mid->id]);

        $ancestors = $leaf->ancestors();

        $this->assertCount(2, $ancestors);
        $this->assertEquals('Root', $ancestors[0]->name);
        $this->assertEquals('Mid', $ancestors[1]->name);
    }
}
