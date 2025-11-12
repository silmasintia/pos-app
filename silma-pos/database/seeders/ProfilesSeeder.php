<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profiles;

class ProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profiles::create([
            'profile_name' => 'Toko ABC',
            'alias' => 'TokoABC',
            'identity_number' => '123456789',
            'address' => 'Jl. Contoh No.1',
            'phone_number' => '08123456789',
            'whatsapp_number' => '08123456789',
            'email' => 'toko@example.com',
            'website' => 'https://tokoabc.com',
            'description_1' => 'Toko terbaik',
            'description_2' => 'Murah & Berkualitas',
            'description_3' => 'Pelayanan Cepat',
            'logo' => 'logo.png',
            'logo_dark' => 'logo_dark.png',
            'favicon' => 'favicon.png',
            'banner' => 'banner.png',
            'login_background' => 'login_bg.png',
            'theme' => 'light',
            'theme_color' => '#4CAF50',
            'boxed_layout' => false,
            'sidebar_type' => 'default',
            'card_border' => true,
            'direction' => 'ltr',
            'embed_youtube' => null,
            'embed_map' => null,
            'keyword' => 'toko, penjualan, pos',
            'keyword_description' => 'Toko ABC menjual berbagai barang berkualitas',
        ]);
    }
}
