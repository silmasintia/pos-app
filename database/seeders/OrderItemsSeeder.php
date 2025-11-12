<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderItems;

class OrderItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderItems::create([
            'order_id' => 1,
            'product_id' => 1,
            'quantity' => 2,
            'order_price' => 50000,
            'total_price' => 100000,
        ]);
    }
}