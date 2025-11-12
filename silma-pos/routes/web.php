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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

// Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/sales-chart-data', [DashboardController::class, 'getSalesChartData'])->name('dashboard.sales.chart');
Route::get('/dashboard/earnings-chart-data', [DashboardController::class, 'getEarningsChartData'])->name('dashboard.earnings.chart');
Route::get('/dashboard/conversion-chart-data', [DashboardController::class, 'getConversionChartData'])->name('dashboard.conversion.chart');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data', [UserController::class, 'getUsersData'])->name('users.data');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Product Management 
    Route::prefix('product-management')->name('product-management.')->group(function () {
        // Categories
        Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
        Route::get('/categories/data', [CategoriesController::class, 'data'])->name('categories.data');
        Route::post('/categories/store', [CategoriesController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoriesController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');

        // Products
        Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
        Route::get('/products/data', [ProductsController::class, 'getProducts'])->name('products.data');
        Route::get('/products/cards', [ProductsController::class, 'getProductsCards'])->name('products.cards');
        Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductsController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductsController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductsController::class, 'destroy'])->name('products.destroy');
    });

    // Customer Management 
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/data', [CustomerController::class, 'getCustomersData'])->name('data');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // Supplier Management 
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/data', [SupplierController::class, 'getSuppliersData'])->name('data');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
    });

    // Sales (POS) 
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/data', [OrderController::class, 'getOrdersData'])->name('data');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/products', [OrderController::class, 'getProducts'])->name('products');
        Route::get('/products/{id}', [OrderController::class, 'getProduct'])->name('product');
        Route::get('/print/{id}', [OrderController::class, 'printReceipt'])->name('print');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::put('/{id}/status', [OrderController::class, 'updateStatus'])->name('update.status');
    });

    // Cash Management 
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::get('/', [CashController::class, 'index'])->name('index');
        Route::get('/data', [CashController::class, 'data'])->name('data');
        Route::post('/', [CashController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CashController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CashController::class, 'update'])->name('update');
        Route::delete('/{id}', [CashController::class, 'destroy'])->name('destroy');
    });

    // Transactions Management 
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/data', [TransactionController::class, 'data'])->name('data');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');
    });

    // Purchase Management 
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/data', [PurchaseController::class, 'getPurchasesData'])->name('data');
        Route::post('/', [PurchaseController::class, 'store'])->name('store');
        Route::get('/products', [PurchaseController::class, 'getProducts'])->name('products');
        Route::get('/{id}', [PurchaseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PurchaseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PurchaseController::class, 'update'])->name('update');
        Route::delete('/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
    });

    // Stock Opname Management 
    Route::prefix('stock-opname')->name('stock-opname.')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::get('/data', [StockOpnameController::class, 'data'])->name('data');
        Route::post('/', [StockOpnameController::class, 'store'])->name('store');
        Route::get('/products', [StockOpnameController::class, 'getProducts'])->name('products');
        Route::get('/{id}', [StockOpnameController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [StockOpnameController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StockOpnameController::class, 'update'])->name('update');
        Route::delete('/{id}', [StockOpnameController::class, 'destroy'])->name('destroy');
    });

    // Adjustment Management 
    Route::prefix('adjustment')->name('adjustment.')->group(function () {
        Route::get('/', [AdjustmentController::class, 'index'])->name('index');
        Route::get('/data', [AdjustmentController::class, 'data'])->name('data');
        Route::post('/', [AdjustmentController::class, 'store'])->name('store');
        Route::get('/products', [AdjustmentController::class, 'getProducts'])->name('products');
        Route::get('/{id}', [AdjustmentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdjustmentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdjustmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdjustmentController::class, 'destroy'])->name('destroy');
    });

    // Reports 
    Route::prefix('reports')->name('reports.')->group(function () {
        // Sales
        Route::get('/sales', [SalesReportController::class, 'sales'])->name('sales');
        Route::get('/sales/data', [SalesReportController::class, 'salesData'])->name('sales.data');
        Route::get('/sales/show/{id}', [SalesReportController::class, 'showOrder'])->name('sales.show');

        // Purchases
        Route::get('/purchases', [PurchasesReportController::class, 'purchases'])->name('purchases');
        Route::get('/purchases/data', [PurchasesReportController::class, 'purchasesData'])->name('purchases.data');
        Route::get('/purchases/show/{id}', [PurchasesReportController::class, 'showPurchase'])->name('purchases.show');

        // Profit & Loss
        Route::get('/profit-loss', [ProfitLossReportController::class, 'index'])->name('profit-loss');
        Route::get('/profit-loss/data', [ProfitLossReportController::class, 'getProfitLossData'])->name('profit-loss.data');
        Route::get('/profit-loss/summary', [ProfitLossReportController::class, 'getSummary'])->name('profit-loss.summary');

        // Log History
        Route::get('/log-histories', [LogHistoryReportController::class, 'index'])->name('log-histories');
        Route::get('/log-histories/data', [LogHistoryReportController::class, 'data'])->name('log-histories.data');
        Route::get('/log-histories/show/{id}', [LogHistoryReportController::class, 'show'])->name('log-histories.show');
    });

    // Profile Management 
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/data', [ProfileController::class, 'data'])->name('data');
        Route::put('/update/{id}', [ProfileController::class, 'update'])->name('update');
        Route::get('/show/{id}', [ProfileController::class, 'show'])->name('show');
    });

    // Social Media Management 
    Route::prefix('social-media')->name('social-media.')->group(function () {
        Route::get('/', [SocialMediaController::class, 'index'])->name('index');
        Route::get('/data', [SocialMediaController::class, 'data'])->name('data');
        Route::post('/store', [SocialMediaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SocialMediaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SocialMediaController::class, 'update'])->name('update');
        Route::get('/show/{id}', [SocialMediaController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [SocialMediaController::class, 'delete'])->name('delete');
    });
});

require __DIR__ . '/auth.php';
