<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\ProfitLoss;

class ProfitLossSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfitLoss::create([
            'cash_id' => 1,
            'transaction_id' => 1,
            'order_id' => 1,
            'purchase_id' => 1,
            'date' => Carbon::now(),
            'category' => 'Penjualan',
            'amount' => 150000,
        ]);
    }
}
