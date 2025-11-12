<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customers;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customers::create([
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'phone' => '08123456789',
            'customer_category_id' => 1,
        ]);

        Customers::create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '08234567890',
            'customer_category_id' => 2,
        ]);
    }
}
