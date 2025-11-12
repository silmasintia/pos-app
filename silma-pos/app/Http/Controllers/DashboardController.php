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

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Orders::whereDate('order_date', today())->sum('total_cost');
        $monthSales = Orders::whereMonth('order_date', now()->month)->sum('total_cost');
        $totalSales = Orders::sum('total_cost');
        
        $totalPurchases = Purchases::sum('total_cost');
        
        $totalProfit = $totalSales - $totalPurchases;
        
        $totalExpenses = Transactions::whereHas('transactionCategory', function($query) {
            $query->where('parent_type', 'expense');
        })->sum('amount');
        
        $netIncome = $totalProfit - $totalExpenses;
        
        $totalProducts = Products::count();
        $lowStockProducts = Products::where('base_stock', '<', 10)->count();
        $outOfStockProducts = Products::where('base_stock', '<=', 0)->count();

        $totalCustomers = Customers::count();
        $newCustomersThisMonth = Customers::whereMonth('created_at', now()->month)->count();

        $totalSuppliers = Suppliers::count();
        $totalPurchasesCount = Purchases::count();

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
            $sales = Orders::whereDate('order_date', $date)->sum('total_cost');
            $purchases = Purchases::whereDate('purchase_date', $date)->sum('total_cost');
            
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
            $totalProducts = $category->products_count;
            $lowStock = $category->products()->where('base_stock', '<', 10)->count();
            $outOfStock = $category->products()->where('base_stock', '<=', 0)->count();

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