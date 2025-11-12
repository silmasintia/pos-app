<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Orders;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Orders::create([
            'order_date' => now(),
            'order_number' => 'ORD-001',
            'customer_id' => 1,
            'cash_id' => 1,
            'total_cost_before' => 100000,
            'percent_discount' => 10,
            'amount_discount' => 10000,
            'input_payment' => 90000,
            'return_payment' => 0,
            'total_cost' => 90000,
            'status' => 'paid',
        ]);
    }
}
