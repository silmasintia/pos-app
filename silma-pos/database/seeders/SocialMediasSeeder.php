<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialMedias;

class SocialMediasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialMedias::create([
            'profile_id' => 1,
            'name' => 'Instagram',
            'description' => 'Akun Instagram Toko ABC',
            'link' => 'https://instagram.com/tokoabc',
            'image' => 'instagram.png',
        ]);
    }
}
