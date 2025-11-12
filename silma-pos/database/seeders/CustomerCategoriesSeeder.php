<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerCategories;

class CustomerCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CustomerCategories::create(['name' => 'Umum']);
        CustomerCategories::create(['name' => 'Member']);
        CustomerCategories::create(['name' => 'VIP']);
    }
}
