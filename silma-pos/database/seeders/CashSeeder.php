<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cash;

class CashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cash::create(['name' => 'Kas Utama', 'amount' => 1000000]);
        Cash::create(['name' => 'Kas Cabang', 'amount' => 500000]);

    }
}
