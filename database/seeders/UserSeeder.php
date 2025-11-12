<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default users
        $users = [
            [
                'name' => 'Admin POS',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'about' => 'Super admin untuk aplikasi POS',
                'description' => 'Memiliki akses penuh ke sistem',
                'address' => 'Jl. Contoh No. 123, Jakarta',
                'phone_number' => '08123456789',
                'wa_number' => '08123456789',
                'status' => true,
                'status_display' => true,
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir 1',
                'username' => 'kasir1',
                'email' => 'kasir1@example.com',
                'password' => Hash::make('password'),
                'about' => 'Kasir toko',
                'description' => 'Bertugas melayani transaksi',
                'address' => 'Jl. Melati No. 45, Bandung',
                'phone_number' => '082345678910',
                'wa_number' => '082345678910',
                'status' => true,
                'status_display' => true,
                'role' => 'kasir',
            ],
            [
                'name' => 'Petugas Gudang',
                'username' => 'gudang1',
                'email' => 'gudang1@example.com',
                'password' => Hash::make('password'),
                'about' => 'Petugas gudang toko',
                'description' => 'Bertanggung jawab atas stok dan barang masuk/keluar',
                'address' => 'Jl. Anggrek No. 12, Surabaya',
                'phone_number' => '083345678912',
                'wa_number' => '083345678912',
                'status' => true,
                'status_display' => true,
                'role' => 'gudang',
            ],
        ];

        // Ensure roles exist
        foreach (['admin', 'kasir', 'gudang'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Create users and assign roles
        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                collect($data)->except('role')->toArray()
            );

            $user->assignRole($data['role']);
        }

        $this->command->info('✅ User Admin, Kasir, dan Gudang berhasil dibuat dan diberi role masing-masing.');
    }
}