<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchases;

class PurchasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Purchases::updateOrCreate(
            ['purchase_number' => 'PUR-001'], 
            [
                'purchase_date' => now(),
                'supplier_id' => 1,
                'cash_id' => 1,
                'total_cost' => 150000,
                'status' => 'paid',
                'description' => 'Pembelian awal',
                'type_payment' => 'cash',
            ]
        );

        Purchases::updateOrCreate(
            ['purchase_number' => 'PUR-002'],
            [
                'purchase_date' => now(),
                'supplier_id' => 2,
                'cash_id' => 1,
                'total_cost' => 200000,
                'status' => 'pending',
                'description' => 'Pembelian tambahan',
                'type_payment' => 'transfer',
            ]
        );
    }
}
