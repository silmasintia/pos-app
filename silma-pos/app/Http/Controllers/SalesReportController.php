<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Customers;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class SalesReportController extends Controller
{
    public function sales()
    {
        $customers = Customers::all();
        return view('dashboard.reports.sales', compact('customers'));
    }

    public function salesData(Request $request)
    {
        $query = Orders::select(
                'orders.id',
                'orders.order_number',
                'orders.order_date',
                'orders.customer_id',
                'orders.status',
                'orders.type_payment',
                'orders.total_cost_before',
                'orders.amount_discount',
                'orders.total_cost',
                'customers.name as customer_name'
            )
            ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
            ->with('items');

        if (!empty($request->date_from)) {
            $query->whereDate('orders.order_date', '>=', Carbon::parse($request->date_from));
        }
        if (!empty($request->date_to)) {
            $query->whereDate('orders.order_date', '<=', Carbon::parse($request->date_to));
        }

        if (!empty($request->customer_id)) {
            $query->where('orders.customer_id', $request->customer_id);
        }

        if (!empty($request->status)) {
            $query->where('orders.status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('order_date', function ($order) {
                return Carbon::parse($order->order_date)->format('d-m-Y');
            })
            ->addColumn('total_items', function ($order) {
                return $order->items->sum('quantity');
            })
            ->editColumn('subtotal', function ($order) {
                return 'Rp ' . number_format($order->total_cost_before, 0, ',', '.');
            })
            ->editColumn('discount', function ($order) {
                return 'Rp ' . number_format($order->amount_discount, 0, ',', '.');
            })
            ->editColumn('total_cost', function ($order) {
                return 'Rp ' . number_format($order->total_cost, 0, ',', '.');
            })
            ->editColumn('payment_type', function ($order) {
                return ucfirst($order->type_payment);
            })
            ->editColumn('status', function ($order) {
                switch ($order->status) {
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
            ->addColumn('action', function ($order) {
                return '<button class="btn btn-sm btn-info view-order-btn" data-id="'.$order->id.'">
                            <i class="fas fa-eye"></i>
                        </button>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function showOrder($id)
    {
        $order = Orders::with(['customer', 'items.product'])->findOrFail($id);

        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'order_date' => Carbon::parse($order->order_date)->format('d-m-Y'),
            'customer_name' => $order->customer ? $order->customer->name : '-',
            'status' => $order->status,
            'status_html' => '',
            'payment_type' => ucfirst($order->type_payment),
            'subtotal' => 'Rp ' . number_format($order->total_cost_before, 0, ',', '.'),
            'discount' => 'Rp ' . number_format($order->amount_discount, 0, ',', '.'),
            'total_cost' => 'Rp ' . number_format($order->total_cost, 0, ',', '.'),
            'input_payment' => 'Rp ' . number_format($order->input_payment, 0, ',', '.'),
            'return_payment' => 'Rp ' . number_format($order->return_payment, 0, ',', '.'),
            'items' => []
        ];

        if ($order->status == 'completed') {
            $data['status_html'] = '<span class="badge bg-success">Completed</span>';
        } elseif ($order->status == 'pending') {
            $data['status_html'] = '<span class="badge bg-warning">Pending</span>';
        } elseif ($order->status == 'cancelled') {
            $data['status_html'] = '<span class="badge bg-danger">Cancelled</span>';
        }

        foreach ($order->items as $item) {
            $data['items'][] = [
                'product_name' => $item->product ? $item->product->name : '-',
                'order_price' => 'Rp ' . number_format($item->order_price, 0, ',', '.'),
                'quantity' => $item->quantity,
                'total_price' => 'Rp ' . number_format($item->total_price, 0, ',', '.')
            ];
        }

        return response()->json($data);
    }
}