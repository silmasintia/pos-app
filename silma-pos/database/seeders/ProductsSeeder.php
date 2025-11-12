<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Products;
use App\Models\Categories;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Categories::first(); 

        if ($category) {
            Products::create([
                'product_code' => 'PRD001',
                'barcode' => '1234567890123',
                'name' => 'Indomie Goreng',
                'slug' => Str::slug('Indomie Goreng'),
                'category_id' => $category->id,
                'description' => 'Mie instan goreng paling laris.',
                'status_active' => true,
                'status_discount' => false,
                'status_display' => true,
                'base_stock' => 100,
            ]);

            Products::create([
                'product_code' => 'PRD002',
                'barcode' => '9876543210987',
                'name' => 'Teh Botol Sosro',
                'slug' => Str::slug('Teh Botol Sosro'),
                'category_id' => $category->id,
                'description' => 'Minuman teh dalam botol.',
                'status_active' => true,
                'status_discount' => true,
                'status_display' => true,
                'base_stock' => 50,
            ]);
        }
    }
}
