<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin POS',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), 
            'image' => null,
            'banner' => null,
            'about' => 'Super admin untuk aplikasi POS',
            'description' => 'Memiliki akses penuh ke sistem',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'phone_number' => '08123456789',
            'wa_number' => '08123456789',
            'status' => true,
            'status_display' => true,
        ]);

        
        User::create([
            'name' => 'Kasir 1',
            'username' => 'kasir1',
            'email' => 'kasir1@example.com',
            'password' => Hash::make('password'),
            'image' => null,
            'banner' => null,
            'about' => 'Kasir toko',
            'description' => 'Bertugas melayani transaksi',
            'address' => 'Jl. Melati No. 45, Bandung',
            'phone_number' => '082345678910',
            'wa_number' => '082345678910',
            'status' => true,
            'status_display' => true,
        ]);
    }
}