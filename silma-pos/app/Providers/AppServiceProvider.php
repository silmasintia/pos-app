<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Products;
use App\Models\Categories;
use App\Models\Customers;
use App\Models\Suppliers;
use App\Models\Orders;
use App\Models\Purchases;
use App\Models\Adjustments;
use App\Models\AdjustmentDetails;
use App\Models\Cash;
use App\Models\CustomerCategories;
use App\Models\OrderItems;
use App\Models\ProductImages;
use App\Models\ProductUnits;
use App\Models\Profiles;
use App\Models\ProfitLoss;
use App\Models\PurchaseItems;
use App\Models\SocialMedias;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\TransactionCategories;
use App\Models\Transactions;
use App\Models\Units;
use App\Observers\LogHistoryObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(LogHistoryObserver::class);
        Products::observe(LogHistoryObserver::class);
        Categories::observe(LogHistoryObserver::class);
        Customers::observe(LogHistoryObserver::class);
        Suppliers::observe(LogHistoryObserver::class);
        Orders::observe(LogHistoryObserver::class);
        Purchases::observe(LogHistoryObserver::class);
        Adjustments::observe(LogHistoryObserver::class);
        AdjustmentDetails::observe(LogHistoryObserver::class);
        Cash::observe(LogHistoryObserver::class);
        CustomerCategories::observe(LogHistoryObserver::class);
        OrderItems::observe(LogHistoryObserver::class);
        ProductImages::observe(LogHistoryObserver::class);
        ProductUnits::observe(LogHistoryObserver::class);
        Profiles::observe(LogHistoryObserver::class);
        ProfitLoss::observe(LogHistoryObserver::class);
        PurchaseItems::observe(LogHistoryObserver::class);
        SocialMedias::observe(LogHistoryObserver::class);
        StockOpname::observe(LogHistoryObserver::class);
        StockOpnameDetail::observe(LogHistoryObserver::class);
        TransactionCategories::observe(LogHistoryObserver::class);
        Transactions::observe(LogHistoryObserver::class);
        Units::observe(LogHistoryObserver::class);
    }
}