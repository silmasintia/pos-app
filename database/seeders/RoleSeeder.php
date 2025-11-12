<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $gudang = Role::firstOrCreate(['name' => 'gudang', 'guard_name' => 'web']);
        $kasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        
        // Get all permissions
        $permissions = Permission::all();
        
        // Assign all permissions to admin
        $admin->syncPermissions($permissions);
        
        // Assign specific permissions to gudang
        $gudangPermissions = [
            'access-dashboard',
            'view-products',
            'create-products',
            'edit-products',
            'view-categories',
            'create-categories',
            'edit-categories',
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'view-purchases',
            'create-purchases',
            'edit-purchases',
            'view-stock-opname',
            'create-stock-opname',
            'edit-stock-opname',
            'view-adjustments',
            'create-adjustments',
            'edit-adjustments',
            'view-profile',
            'edit-profile',
        ];
        $gudang->syncPermissions($gudangPermissions);
        
        // Assign specific permissions to kasir
        $kasirPermissions = [
            'access-dashboard',
            'view-customers',
            'create-customers',
            'edit-customers',
            'view-sales',
            'create-sales',
            'view-cash',
            'create-cash',
            'edit-cash',
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'view-profile',
            'edit-profile',
        ];
        $kasir->syncPermissions($kasirPermissions);
    }
}