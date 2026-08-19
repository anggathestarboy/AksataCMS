<?php

namespace Database\Seeders;

use App\Models\SectionType;
use Illuminate\Database\Seeder;

class SectionTypeSeeder extends Seeder
{
    public function run(): void
    {
        SectionType::create([
            'name' => 'Hero Section',
            'slug' => 'hero-section',
            'icon' => 'image',
            'fields' => [
                ['key' => 'title', 'type' => 'text', 'label' => 'Title', 'required' => true],
                ['key' => 'desc', 'type' => 'textarea', 'label' => 'Desc', 'required' => true],
                ['key' => 'image', 'type' => 'image', 'label' => 'Image', 'required' => true],
            ],
        ]);

        SectionType::create([
            'name' => 'Text + image',
            'slug' => 'text-image',
            'icon' => 'image',
            'fields' => [
                ['key' => 'image', 'type' => 'image', 'label' => 'Image', 'required' => true],
                ['key' => 'title', 'type' => 'text', 'label' => 'Title', 'required' => true],
                ['key' => 'desc', 'type' => 'textarea', 'label' => 'Desc', 'required' => true],
            ],
        ]);

        SectionType::create([
            'name' => 'Acordion',
            'slug' => 'acordion',
            'icon' => 'list',
            'fields' => [
                ['key' => 'heading', 'type' => 'text', 'label' => 'Heading', 'required' => true],
                ['key' => 'desc', 'type' => 'text', 'label' => 'Desc', 'required' => true],
            ],
        ]);

        SectionType::create([
            'name' => 'card',
            'slug' => 'card',
            'icon' => 'list',
            'fields' => [
                ['key' => 'card-heading', 'type' => 'text', 'label' => 'Heading Card', 'required' => false],
                ['key' => 'card-desc', 'type' => 'text', 'label' => 'Card Desc', 'required' => false],
                [
                    'key' => 'list-card',
                    'type' => 'repeater',
                    'label' => 'List Card',
                    'required' => true,
                    'fields' => [
                        ['key' => 'heading-card', 'type' => 'text', 'label' => 'Heading Card', 'required' => true],
                        ['key' => 'desc-card', 'type' => 'textarea', 'label' => 'Desc Card', 'required' => true],
                        ['key' => 'image-card', 'type' => 'image', 'label' => 'Image', 'required' => false],
                        ['key' => 'link', 'type' => 'link', 'label' => 'Link', 'required' => false],
                    ],
                ],
            ],
        ]);

        SectionType::create([
            'name' => 'Overview',
            'slug' => 'overview',
            'icon' => 'sparkles',
            'fields' => [
                ['key' => 'heading', 'type' => 'text', 'label' => 'Heading', 'required' => true],
                ['key' => 'desc', 'type' => 'textarea', 'label' => 'Desc', 'required' => true],
            ],
        ]);
    }
}
