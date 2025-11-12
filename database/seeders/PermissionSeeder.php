<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permission untuk Dashboard
        Permission::firstOrCreate(['name' => 'access-dashboard', 'guard_name' => 'web']);
        
        // Permission untuk User Management
        Permission::firstOrCreate(['name' => 'view-users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-users', 'guard_name' => 'web']);
        
        // Permission untuk Product Management
        Permission::firstOrCreate(['name' => 'view-products', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-products', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-products', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-products', 'guard_name' => 'web']);
        
        // Permission untuk Category Management
        Permission::firstOrCreate(['name' => 'view-categories', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-categories', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-categories', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-categories', 'guard_name' => 'web']);
        
        // Permission untuk Customer Management
        Permission::firstOrCreate(['name' => 'view-customers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-customers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-customers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-customers', 'guard_name' => 'web']);
        
        // Permission untuk Supplier Management
        Permission::firstOrCreate(['name' => 'view-suppliers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-suppliers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-suppliers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-suppliers', 'guard_name' => 'web']);
        
        // Permission untuk Sales (POS)
        Permission::firstOrCreate(['name' => 'view-sales', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-sales', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-sales', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-sales', 'guard_name' => 'web']);
        
        // Permission untuk Purchases
        Permission::firstOrCreate(['name' => 'view-purchases', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-purchases', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-purchases', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-purchases', 'guard_name' => 'web']);
        
        // Permission untuk Cash Management
        Permission::firstOrCreate(['name' => 'view-cash', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-cash', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-cash', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-cash', 'guard_name' => 'web']);
        
        // Permission untuk Transactions
        Permission::firstOrCreate(['name' => 'view-transactions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-transactions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-transactions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-transactions', 'guard_name' => 'web']);
        
        // Permission untuk Stock Opname
        Permission::firstOrCreate(['name' => 'view-stock-opname', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-stock-opname', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-stock-opname', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-stock-opname', 'guard_name' => 'web']);
        
        // Permission untuk Adjustments
        Permission::firstOrCreate(['name' => 'view-adjustments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-adjustments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-adjustments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-adjustments', 'guard_name' => 'web']);
        
        // Permission untuk Reports
        Permission::firstOrCreate(['name' => 'view-sales-reports', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-purchases-reports', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-profit-loss-reports', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-log-histories', 'guard_name' => 'web']);
        
        // Permission untuk Profile & Social Media
        Permission::firstOrCreate(['name' => 'view-profile', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-profile', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-social-media', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-social-media', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-social-media', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-social-media', 'guard_name' => 'web']);
        
        // Permission untuk Role & Permission Management
        Permission::firstOrCreate(['name' => 'view-roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-permissions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create-permissions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-permissions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete-permissions', 'guard_name' => 'web']);
    }
}