<?php

namespace Tests\Unit;

use App\Helpers\FieldHelper;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldHelperImageMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_field_returns_url_from_string_path(): void
    {
        $content = ['photo' => 'media/test.webp'];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo');

        $this->assertStringContainsString('/storage/media/test.webp', $result);
    }

    public function test_image_field_returns_url_from_array_path(): void
    {
        $content = ['photo' => ['path' => 'media/test.webp', 'width' => 800, 'height' => 600]];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo');

        $this->assertStringContainsString('/storage/media/test.webp', $result);
    }

    public function test_image_width_returns_from_array(): void
    {
        $content = ['photo' => ['path' => 'media/test.webp', 'width' => 1920, 'height' => 1080]];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo_width');

        $this->assertEquals('1920', $result);
    }

    public function test_image_height_returns_from_array(): void
    {
        $content = ['photo' => ['path' => 'media/test.webp', 'width' => 1920, 'height' => 1080]];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo_height');

        $this->assertEquals('1080', $result);
    }

    public function test_image_alt_returns_from_array(): void
    {
        $content = ['photo' => ['path' => 'media/test.webp', 'alt' => 'My Image']];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo_alt');

        $this->assertEquals('My Image', $result);
    }

    public function test_image_loading_returns_from_array(): void
    {
        $content = ['photo' => ['path' => 'media/test.webp', 'loading' => 'eager']];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo_loading');

        $this->assertEquals('eager', $result);
    }

    public function test_image_property_returns_empty_for_missing_key(): void
    {
        $content = ['photo' => ''];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo_width');

        $this->assertEquals('', $result);
    }

    public function test_image_url_returns_empty_for_empty_value(): void
    {
        $content = ['photo' => ''];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo');

        $this->assertEquals('', $result);
    }

    public function test_image_property_looks_up_media_model_for_string_path(): void
    {
        Media::create([
            'name' => 'Test',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'path' => 'media/lookup-test.webp',
            'width' => 640,
            'height' => 480,
            'loading' => 'lazy',
            'slug' => 'lookup-test',
        ]);

        $content = ['photo' => 'media/lookup-test.webp'];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $this->assertEquals('640', FieldHelper::render($content, $fields, 'photo_width'));
        $this->assertEquals('480', FieldHelper::render($content, $fields, 'photo_height'));
        $this->assertEquals('lazy', FieldHelper::render($content, $fields, 'photo_loading'));
    }

    public function test_non_image_field_with_suffix_is_not_affected(): void
    {
        $content = ['title' => 'Hello World'];
        $fields = [['key' => 'title', 'type' => 'text', 'label' => 'Title']];

        $result = FieldHelper::render($content, $fields, 'title');

        $this->assertEquals('Hello World', $result);
    }

    public function test_link_field_sub_keys_still_work(): void
    {
        $content = ['cta' => [
            'label' => 'Click Me',
            'url' => '/about',
            'link_type' => 'internal',
            'open_in_new_tab' => false,
        ]];
        $fields = [['key' => 'cta', 'type' => 'link', 'label' => 'CTA']];

        $this->assertEquals('/about', FieldHelper::render($content, $fields, 'cta_url'));
        $this->assertEquals('Click Me', FieldHelper::render($content, $fields, 'cta_label'));
        $this->assertEquals('', FieldHelper::render($content, $fields, 'cta_target'));
    }

    public function test_image_field_with_http_url_returns_as_is(): void
    {
        $content = ['photo' => 'https://example.com/image.jpg'];
        $fields = [['key' => 'photo', 'type' => 'image', 'label' => 'Photo']];

        $result = FieldHelper::render($content, $fields, 'photo');

        $this->assertEquals('https://example.com/image.jpg', $result);
    }
}
