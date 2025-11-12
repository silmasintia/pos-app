<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Products;
use App\Models\ProductImages;

class ProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Products::first();

        if ($product) {
            ProductImages::create([
                'product_id' => $product->id,
                'image' => 'products/indomie-goreng-1.jpg',
                'description' => 'Tampak depan',
                'sort_order' => 1,
            ]);

            ProductImages::create([
                'product_id' => $product->id,
                'image' => 'products/indomie-goreng-2.jpg',
                'description' => 'Tampak belakang',
                'sort_order' => 2,
            ]);
        }
    }
}
