<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('site_title', 'Content Blog CMS');
        Setting::set('site_footer', '© 2026 Content Blog Page. Hak Cipta Dilindungi.');
        Setting::set('default_seo_image', 'settings/uY9vgCKYEyBSCAcPnhTfV61IUGk1hp8IZvkC8Qi0.webp');
    }
}
