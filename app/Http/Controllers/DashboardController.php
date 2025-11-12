<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Products;
use App\Models\Customers;
use App\Models\Suppliers;
use App\Models\Categories;
use App\Models\Cash;
use App\Models\Transactions;
use App\Models\StockOpname;
use App\Models\Adjustments;
use App\Models\Purchases;
use App\Models\LogHistory;
use App\Models\ProfitLoss;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:access-dashboard')->only(['index', 'getSalesChartData', 'getEarningsChartData', 'getConversionChartData']);
    }

    public function index()
    {
        $todaySales = (float) Orders::whereDate('order_date', today())->sum('total_cost');
        $monthSales = (float) Orders::whereMonth('order_date', now()->month)->sum('total_cost');
        $totalSales = (float) Orders::sum('total_cost');
        
        $totalPurchases = (float) Purchases::sum('total_cost');
        
        $totalProfit = $totalSales - $totalPurchases;
        
        $totalExpenses = (float) Transactions::whereHas('transactionCategory', function($query) {
            $query->where('parent_type', 'expense');
        })->sum('amount');
        
        $netIncome = $totalProfit - $totalExpenses;
        
        $totalProducts = (int) Products::count();
        $lowStockProducts = (int) Products::where('base_stock', '<', 10)->count();
        $outOfStockProducts = (int) Products::where('base_stock', '<=', 0)->count();

        $totalCustomers = (int) Customers::count();
        $newCustomersThisMonth = (int) Customers::whereMonth('created_at', now()->month)->count();

        $totalSuppliers = (int) Suppliers::count();
        $totalPurchasesCount = (int) Purchases::count();

        $recentOrders = Orders::with('customer')->latest()->take(5)->get();
        $recentTransactions = Transactions::with('cash')->latest()->take(5)->get();
        $recentLogHistories = LogHistory::with('userRelation')->latest()->take(7)->get();

        $topSellingProducts = Products::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        $salesData = $this->getSalesData();
        $stockStatus = $this->getStockStatus();

        return view('dashboard.index', compact(
            'todaySales',
            'monthSales',
            'totalSales',
            'totalPurchases',
            'totalProfit',
            'totalExpenses',
            'netIncome',
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'totalCustomers',
            'newCustomersThisMonth',
            'totalSuppliers',
            'totalPurchasesCount',
            'recentOrders',
            'recentTransactions',
            'recentLogHistories',
            'topSellingProducts',
            'salesData',
            'stockStatus'
        ));
    }

    private function getSalesData()
    {
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $sales = (float) Orders::whereDate('order_date', $date)->sum('total_cost');
            $purchases = (float) Purchases::whereDate('purchase_date', $date)->sum('total_cost');
            
            $salesData['labels'][] = now()->subDays($i)->format('D, d M');
            $salesData['sales'][] = $sales;
            $salesData['purchases'][] = $purchases;
        }
        return $salesData;
    }

    private function getStockStatus()
    {
        $categories = Categories::withCount('products')->get();
        $stockStatus = [];

        foreach ($categories as $category) {
            $totalProducts = (int) $category->products_count;
            $lowStock = (int) $category->products()->where('base_stock', '<', 10)->count();
            $outOfStock = (int) $category->products()->where('base_stock', '<=', 0)->count();

            $stockStatus[] = [
                'category' => $category->name,
                'total' => $totalProducts,
                'low_stock' => $lowStock,
                'out_of_stock' => $outOfStock,
                'percentage' => $totalProducts > 0 ? round(($lowStock + $outOfStock) / $totalProducts * 100) : 0
            ];
        }

        return $stockStatus;
    }

    public function getSalesChartData()
    {
        $salesData = $this->getSalesData();

        return response()->json([
            'labels' => $salesData['labels'],
            'sales' => $salesData['sales'],
            'purchases' => $salesData['purchases']
        ]);
    }
}