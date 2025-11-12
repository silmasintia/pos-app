<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchases;
use App\Models\Suppliers;
use App\Models\PurchaseItems;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class PurchasesReportController extends Controller
{
    public function purchases()
    {
        $suppliers = Suppliers::all();
        return view('dashboard.reports.purchases', compact('suppliers'));
    }

    public function purchasesData(Request $request)
    {
        $query = Purchases::select(
                'purchases.id',
                'purchases.purchase_number',
                'purchases.purchase_date',
                'purchases.supplier_id',
                'purchases.status',
                'purchases.type_payment',
                'purchases.total_cost',
                'suppliers.name as supplier_name'
            )
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->with('items');

        if (!empty($request->date_from)) {
            $query->whereDate('purchases.purchase_date', '>=', Carbon::parse($request->date_from));
        }
        if (!empty($request->date_to)) {
            $query->whereDate('purchases.purchase_date', '<=', Carbon::parse($request->date_to));
        }

        if (!empty($request->supplier_id)) {
            $query->where('purchases.supplier_id', $request->supplier_id);
        }

        if (!empty($request->status)) {
            $query->where('purchases.status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('purchase_date', function ($purchase) {
                return Carbon::parse($purchase->purchase_date)->format('d-m-Y');
            })
            ->addColumn('total_items', function ($purchase) {
                return $purchase->items->sum('quantity');
            })
            ->editColumn('total_cost', function ($purchase) {
                return 'Rp ' . number_format($purchase->total_cost, 0, ',', '.');
            })
            ->editColumn('payment_type', function ($purchase) {
                return ucfirst($purchase->type_payment);
            })
            ->editColumn('status', function ($purchase) {
                switch ($purchase->status) {
                    case 'completed':
                        return '<span class="badge bg-success">Completed</span>';
                    case 'pending':
                        return '<span class="badge bg-warning">Pending</span>';
                    case 'cancelled':
                        return '<span class="badge bg-danger">Cancelled</span>';
                    default:
                        return '-';
                }
            })
            ->addColumn('action', function ($purchase) {
                return '<button class="btn btn-sm btn-info view-purchase-btn" data-id="'.$purchase->id.'">
                            <i class="fas fa-eye"></i>
                        </button>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function showPurchase($id)
    {
        $purchase = Purchases::with(['supplier', 'items.product'])->findOrFail($id);

        $data = [
            'id' => $purchase->id,
            'purchase_number' => $purchase->purchase_number,
            'purchase_date' => Carbon::parse($purchase->purchase_date)->format('d-m-Y'),
            'supplier_name' => $purchase->supplier ? $purchase->supplier->name : '-',
            'status' => $purchase->status,
            'status_html' => '',
            'payment_type' => ucfirst($purchase->type_payment),
            'total_cost' => 'Rp ' . number_format($purchase->total_cost, 0, ',', '.'),
            'items' => []
        ];

        if ($purchase->status == 'completed') {
            $data['status_html'] = '<span class="badge bg-success">Completed</span>';
        } elseif ($purchase->status == 'pending') {
            $data['status_html'] = '<span class="badge bg-warning">Pending</span>';
        } elseif ($purchase->status == 'cancelled') {
            $data['status_html'] = '<span class="badge bg-danger">Cancelled</span>';
        }

        foreach ($purchase->items as $item) {
            $data['items'][] = [
                'product_name' => $item->product ? $item->product->name : '-',
                'purchase_price' => 'Rp ' . number_format($item->purchase_price, 0, ',', '.'),
                'quantity' => $item->quantity,
                'total_price' => 'Rp ' . number_format($item->total_price, 0, ',', '.')
            ];
        }

        return response()->json($data);
    }
}