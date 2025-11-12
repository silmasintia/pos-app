<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Units;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = ['PCS', 'Dus', 'Box', 'Pack', 'Kg', 'Liter'];

        foreach ($units as $unit) {
            Units::create(['name' => $unit]);
        }

    }
}
