<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Its Anggara',
            'email' => 'anggara@gmail.com',
            'password' => Hash::make('aksata2003'),
        ]);
    }
}
