<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Adjustments;

class AdjustmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adjustments = [
            [
                'adjustment_number' => 'ADJ-001',
                'adjustment_date' => now(),
                'description' => 'Penyesuaian stok awal',
                'total' => 100,
                'image' => null,
            ],
            [
                'adjustment_number' => 'ADJ-002',
                'adjustment_date' => now(),
                'description' => 'Penyesuaian stok hilang',
                'total' => -5,
                'image' => null,
            ],
        ];

        foreach ($adjustments as $adj) {
            Adjustments::updateOrCreate(['adjustment_number' => $adj['adjustment_number']], $adj);
        }
    }
}
