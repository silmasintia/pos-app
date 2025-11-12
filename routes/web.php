<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\PurchasesReportController;
use App\Http\Controllers\ProfitLossReportController;
use App\Http\Controllers\LogHistoryReportController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales-chart-data', [DashboardController::class, 'getSalesChartData'])->name('dashboard.sales.chart');
    Route::get('/dashboard/earnings-chart-data', [DashboardController::class, 'getEarningsChartData'])->name('dashboard.earnings.chart');
    Route::get('/dashboard/conversion-chart-data', [DashboardController::class, 'getConversionChartData'])->name('dashboard.conversion.chart');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data', [UserController::class, 'getUsersData'])->name('users.data');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Product Management
    Route::get('/product-management/products', [ProductsController::class, 'index'])->name('product-management.products.index');
    Route::get('/product-management/products/data', [ProductsController::class, 'getProducts'])->name('product-management.products.data');
    Route::get('/product-management/products/cards', [ProductsController::class, 'getProductsCards'])->name('product-management.products.cards');
    Route::post('/product-management/products', [ProductsController::class, 'store'])->name('product-management.products.store');
    Route::get('/product-management/products/{id}/edit', [ProductsController::class, 'edit'])->name('product-management.products.edit');
    Route::put('/product-management/products/{id}', [ProductsController::class, 'update'])->name('product-management.products.update');
    Route::delete('/product-management/products/{id}', [ProductsController::class, 'destroy'])->name('product-management.products.destroy');

    // Category Management
    Route::get('/product-management/categories', [CategoriesController::class, 'index'])->name('product-management.categories.index');
    Route::get('/product-management/categories/data', [CategoriesController::class, 'data'])->name('product-management.categories.data');
    Route::post('/product-management/categories/store', [CategoriesController::class, 'store'])->name('product-management.categories.store');
    Route::get('/product-management/categories/{id}/edit', [CategoriesController::class, 'edit'])->name('product-management.categories.edit');
    Route::put('/product-management/categories/{id}', [CategoriesController::class, 'update'])->name('product-management.categories.update');
    Route::delete('/product-management/categories/{id}', [CategoriesController::class, 'destroy'])->name('product-management.categories.destroy');

    // Customer Management
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/data', [CustomerController::class, 'getCustomersData'])->name('customers.data');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Supplier Management
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/data', [SupplierController::class, 'getSuppliersData'])->name('suppliers.data');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Sales Management
    Route::get('/sales', [OrderController::class, 'index'])->name('sales.index');
    Route::get('/sales/categories', [OrderController::class, 'getCategories'])->name('sales.categories');
    Route::get('/sales/data', [OrderController::class, 'getOrdersData'])->name('sales.data');
    Route::get('/sales/products', [OrderController::class, 'getProducts'])->name('sales.products');
    Route::get('/sales/products/{id}', [OrderController::class, 'getProduct'])->name('sales.product');
    Route::post('/sales', [OrderController::class, 'store'])->name('sales.store');
    Route::get('/sales/{id}', [OrderController::class, 'show'])->name('sales.show');
    Route::put('/sales/{id}/status', [OrderController::class, 'updateStatus'])->name('sales.update.status');
    Route::get('/sales/print/{id}', [OrderController::class, 'printReceipt'])->name('sales.print');

    // Cash Management
    Route::get('/cash', [CashController::class, 'index'])->name('cash.index');
    Route::get('/cash/data', [CashController::class, 'data'])->name('cash.data');
    Route::post('/cash', [CashController::class, 'store'])->name('cash.store');
    Route::get('/cash/{id}/edit', [CashController::class, 'edit'])->name('cash.edit');
    Route::put('/cash/{id}', [CashController::class, 'update'])->name('cash.update');
    Route::delete('/cash/{id}', [CashController::class, 'destroy'])->name('cash.destroy');

    // Transaction Management
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/data', [TransactionController::class, 'data'])->name('transactions.data');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Purchase Management
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/data', [PurchaseController::class, 'getPurchasesData'])->name('purchases.data');
    Route::get('/purchases/products', [PurchaseController::class, 'getProducts'])->name('purchases.products');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');

    // Stock Opname Management
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::get('/stock-opname/data', [StockOpnameController::class, 'data'])->name('stock-opname.data');
    Route::get('/stock-opname/products', [StockOpnameController::class, 'getProducts'])->name('stock-opname.products');
    Route::post('/stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');
    Route::get('/stock-opname/{id}/edit', [StockOpnameController::class, 'edit'])->name('stock-opname.edit');
    Route::put('/stock-opname/{id}', [StockOpnameController::class, 'update'])->name('stock-opname.update');
    Route::delete('/stock-opname/{id}', [StockOpnameController::class, 'destroy'])->name('stock-opname.destroy');
    Route::get('/stock-opname/{id}', [StockOpnameController::class, 'show'])->name('stock-opname.show');

    // Adjustment Management
    Route::get('/adjustment', [AdjustmentController::class, 'index'])->name('adjustment.index');
    Route::get('/adjustment/data', [AdjustmentController::class, 'data'])->name('adjustment.data');
    Route::get('/adjustment/products', [AdjustmentController::class, 'getProducts'])->name('adjustment.products');
    Route::get('/adjustment/{id}', [AdjustmentController::class, 'show'])->name('adjustment.show');
    Route::post('/adjustment', [AdjustmentController::class, 'store'])->name('adjustment.store');
    Route::get('/adjustment/{id}/edit', [AdjustmentController::class, 'edit'])->name('adjustment.edit');
    Route::put('/adjustment/{id}', [AdjustmentController::class, 'update'])->name('adjustment.update');
    Route::delete('/adjustment/{id}', [AdjustmentController::class, 'destroy'])->name('adjustment.destroy');

    // Sales Reports
    Route::get('/reports/sales', [SalesReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/sales/data', [SalesReportController::class, 'salesData'])->name('reports.sales.data');
    Route::get('/reports/sales/show/{id}', [SalesReportController::class, 'showOrder'])->name('reports.sales.show');

    // Purchases Reports
    Route::get('/reports/purchases', [PurchasesReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('/reports/purchases/data', [PurchasesReportController::class, 'purchasesData'])->name('reports.purchases.data');
    Route::get('/reports/purchases/show/{id}', [PurchasesReportController::class, 'showPurchase'])->name('reports.purchases.show');

    // Profit & Loss Reports
    Route::get('/reports/profit-loss', [ProfitLossReportController::class, 'index'])->name('reports.profit-loss');
    Route::get('/reports/profit-loss/data', [ProfitLossReportController::class, 'getProfitLossData'])->name('reports.profit-loss.data');
    Route::get('/reports/profit-loss/summary', [ProfitLossReportController::class, 'getSummary'])->name('reports.profit-loss.summary');

    // Log History Reports
    Route::get('/reports/log-histories', [LogHistoryReportController::class, 'index'])->name('reports.log-histories');
    Route::get('/reports/log-histories/data', [LogHistoryReportController::class, 'data'])->name('reports.log-histories.data');
    Route::get('/reports/log-histories/show/{id}', [LogHistoryReportController::class, 'show'])->name('reports.log-histories.show');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/data', [ProfileController::class, 'data'])->name('profile.data');
    Route::get('/profile/show/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');

    // Social Media Management
    Route::get('/social-media', [SocialMediaController::class, 'index'])->name('social-media.index');
    Route::get('/social-media/data', [SocialMediaController::class, 'data'])->name('social-media.data');
    Route::get('/social-media/show/{id}', [SocialMediaController::class, 'show'])->name('social-media.show');
    Route::post('/social-media/store', [SocialMediaController::class, 'store'])->name('social-media.store');
    Route::get('/social-media/{id}/edit', [SocialMediaController::class, 'edit'])->name('social-media.edit');
    Route::put('/social-media/update/{id}', [SocialMediaController::class, 'update'])->name('social-media.update');
    Route::delete('/social-media/delete/{id}', [SocialMediaController::class, 'delete'])->name('social-media.delete');

    // Role & Permission Management
    Route::middleware('permission:view-roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/data', [RoleController::class, 'data'])->name('roles.data');
    });

    Route::middleware('permission:create-roles')->group(function () {
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware('permission:edit-roles')->group(function () {
        Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::middleware('permission:delete-roles')->group(function () {
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Permission Management
    Route::middleware('permission:view-permissions')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/data', [PermissionController::class, 'data'])->name('permissions.data');
    });

    Route::middleware('permission:create-permissions')->group(function () {
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    });

    Route::middleware('permission:edit-permissions')->group(function () {
        Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    });

    Route::middleware('permission:delete-permissions')->group(function () {
        Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // API endpoint for getting all permissions
    Route::get('/api/permissions', function () {
        return App\Models\Permission::all();
    })->middleware('permission:view-permissions');
});

Route::get('/run-storage-link', function () {
    Artisan::call('storage:link');
    return "✅ Storage link berhasil dibuat!";
});

require __DIR__ . '/auth.php';
