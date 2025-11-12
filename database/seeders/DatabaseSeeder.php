<?php

namespace Database\Seeders;

use App\Models\AdjustmentDetails;
use App\Models\Adjustments;
use App\Models\LogHistories;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,
            // CategoriesSeeder::class,
            // ProductsSeeder::class,
            // UnitsSeeder::class,
            // ProductUnitsSeeder::class,
            // ProductImagesSeeder::class,
            // CustomerCategoriesSeeder::class,
            // CustomersSeeder::class,
            // SuppliersSeeder::class,
            // CashSeeder::class,
            // OrdersSeeder::class,
            // OrderItemsSeeder::class,
            // PurchasesSeeder::class,
            // PurchaseItemsSeeder::class,
            // TransactionCategoriesSeeder::class,
            // TransactionsSeeder::class,
            // AdjustmentsSeeder::class,
            // AdjustmentDetailsSeeder::class,
            // StockOpnameSeeder::class,
            // StockOpnameDetailSeeder::class,
            // ProfitLossSeeder::class,
            // LogHistoriesSeeder::class,
            // ProfilesSeeder::class,
            // SocialMediasSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class
        ]);
    }
}