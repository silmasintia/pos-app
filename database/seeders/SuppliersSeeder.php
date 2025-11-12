<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Suppliers;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Suppliers::create([
            'name' => 'PT Sumber Makmur',
            'email' => 'sumber@example.com',
            'phone' => '08111111111',
            'address' => 'Jakarta'
        ]);

        Suppliers::create([
            'name' => 'CV Abadi Jaya',
            'email' => 'abadi@example.com',
            'phone' => '08222222222',
            'address' => 'Bandung'
        ]);
    }
}
