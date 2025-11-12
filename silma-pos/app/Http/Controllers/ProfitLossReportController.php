<?php

namespace App\Http\Controllers;

use App\Models\ProfitLoss;
use App\Models\Orders;
use App\Models\Purchases;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ProfitLossReportController extends Controller
{
    public function index()
    {
        return view('dashboard.reports.profit-loss');
    }

    public function getProfitLossData(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $profitLoss = ProfitLoss::with(['cash', 'transaction', 'order', 'purchase'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return DataTables::of($profitLoss)
            ->addColumn('date', function ($item) {
                return Carbon::parse($item->date)->format('d M Y');
            })
            ->addColumn('category', function ($item) {
                return ucfirst($item->category);
            })
            ->addColumn('amount', function ($item) {
                return 'Rp ' . number_format($item->amount, 0, ',', '.');
            })
            ->addColumn('source', function ($item) {
                if ($item->order_id) {
                    return '<span class="badge bg-success">Sales</span>';
                } elseif ($item->purchase_id) {
                    return '<span class="badge bg-danger">Purchase</span>';
                } elseif ($item->transaction_id) {
                    return '<span class="badge bg-info">Transaction</span>';
                }
                return '<span class="badge bg-secondary">Other</span>';
            })
            ->addColumn('reference', function ($item) {
                if ($item->order_id) {
                    return 'Order #' . $item->order->order_number ?? $item->order_id;
                } elseif ($item->purchase_id) {
                    return 'Purchase #' . $item->purchase->purchase_number ?? $item->purchase_id;
                } elseif ($item->transaction_id) {
                    return 'Transaction #' . $item->transaction_id;
                }
                return '-';
            })
            ->addColumn('cash', function ($item) {
                return $item->cash->name ?? '-';
            })
            ->rawColumns(['source'])
            ->make(true);
    }

    public function getSummary(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $totalSales = Orders::whereBetween('order_date', [$startDate, $endDate])
            ->sum('total_cost');

        $totalPurchases = Purchases::whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('total_cost');

        $otherIncome = Transactions::whereBetween('date', [$startDate, $endDate])
            ->where('amount', '>', 0)
            ->sum('amount');

        $otherExpenses = Transactions::whereBetween('date', [$startDate, $endDate])
            ->where('amount', '<', 0)
            ->sum('amount');

        $grossProfit = $totalSales - $totalPurchases;

        $netProfit = $grossProfit + $otherIncome + $otherExpenses;

        return response()->json([
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'otherIncome' => $otherIncome,
            'otherExpenses' => abs($otherExpenses),
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
        ]);
    }
}