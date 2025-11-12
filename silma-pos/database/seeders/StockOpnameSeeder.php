<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StockOpname;

class StockOpnameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opnames = [
            [
                'opname_number' => 'OPN-001',
                'opname_date' => now(),
                'description' => 'Cek stok bulanan',
                'image' => null,
            ],
        ];

        foreach ($opnames as $opn) {
            StockOpname::updateOrCreate(['opname_number' => $opn['opname_number']], $opn);
        }
    }
}
