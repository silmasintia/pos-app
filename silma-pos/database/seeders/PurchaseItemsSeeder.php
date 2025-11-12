<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseItems;

class PurchaseItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PurchaseItems::updateOrCreate(
            ['purchase_id' => 1, 'product_id' => 1], 
            [
                'quantity' => 3,
                'purchase_price' => 50000,
                'total_price' => 150000,
            ]
        );

        PurchaseItems::updateOrCreate(
            ['purchase_id' => 2, 'product_id' => 2],
            [
                'quantity' => 4,
                'purchase_price' => 50000,
                'total_price' => 200000,
            ]
        );
    }
}