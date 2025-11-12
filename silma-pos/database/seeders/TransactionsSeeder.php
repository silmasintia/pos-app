<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transactions;
use App\Models\TransactionCategories;
use App\Models\Cash;

class TransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $cash = Cash::first(); 
        $categoryIn = TransactionCategories::where('name', 'Cash In')->first();
        $categoryOut = TransactionCategories::where('name', 'Cash Out')->first();

        if ($cash && $categoryIn && $categoryOut) {
            Transactions::updateOrCreate(
                ['name' => 'Pembayaran pelanggan'], 
                [
                    'date' => now(),
                    'transaction_category_id' => $categoryIn->id,
                    'cash_id' => $cash->id,
                    'amount' => 500000,
                    'description' => 'Transaksi masuk dari pelanggan',
                ]
            );

            Transactions::updateOrCreate(
                ['name' => 'Pembayaran supplier'], 
                [
                    'date' => now(),
                    'transaction_category_id' => $categoryOut->id,
                    'cash_id' => $cash->id,
                    'amount' => 200000,
                    'description' => 'Transaksi keluar untuk supplier',
                ]
            );
        }
    }
}