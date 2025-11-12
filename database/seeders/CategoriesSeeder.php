<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categories;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Makanan',
            'Minuman',
            'Snack',
            'Frozen Food',
            'Bumbu Dapur'
        ];

        foreach ($categories as $index => $name) {
            Categories::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $name . ' description',
                'image' => null,
                'position' => $index + 1, 
            ]);
        }
    }
}
