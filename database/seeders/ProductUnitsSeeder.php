<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Products;
use App\Models\Units;
use App\Models\ProductUnits;

class ProductUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Products::first();
        $unit = Units::where('name', 'PCS')->first();

        if ($product && $unit) {
            ProductUnits::create([
                'product_id' => $product->id,
                'unit_id' => $unit->id,
                'conversion_factor' => 1,
                'purchase_price' => 2000,
                'cost_price' => 2500,
                'price_before_discount' => 3000,
                'is_base' => true,
            ]);
        }
    }
}
