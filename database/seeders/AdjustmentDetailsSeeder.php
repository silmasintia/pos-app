<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Adjustments;
use App\Models\Products;
use App\Models\AdjustmentDetails;

class AdjustmentDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adjustment = Adjustments::first();
        $product = Products::first();

        if ($adjustment && $product) {
            AdjustmentDetails::updateOrCreate(
                [
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $product->id,
                ],
                [
                    'name' => $product->name,
                    'product_code' => $product->product_code,
                    'quantity' => 10,
                    'reason' => 'Stok awal',
                ]
            );
        }
    }
}
