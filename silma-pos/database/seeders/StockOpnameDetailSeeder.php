<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StockOpname;
use App\Models\Products;
use App\Models\StockOpnameDetail;

class StockOpnameDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opname = StockOpname::first();
        $product = Products::first();

        if ($opname && $product) {
            StockOpnameDetail::updateOrCreate(
                [
                    'stock_opname_id' => $opname->id,
                    'product_id' => $product->id,
                ],
                [
                    'system_stock' => 50,
                    'physical_stock' => 48,
                    'difference' => -2,
                    'description_detail' => 'Stok hilang',
                ]
            );
        }
    }
}
