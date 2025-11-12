<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TransactionCategories;

class TransactionCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $categories = [
            ['name' => 'Cash In', 'parent_type' => 'Income', 'description' => 'Kas masuk'],
            ['name' => 'Cash Out', 'parent_type' => 'Expense', 'description' => 'Kas keluar'],
        ];

        foreach ($categories as $cat) {
            TransactionCategories::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
