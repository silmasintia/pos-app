<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\OrderItems;
use App\Models\Products;
use App\Models\Customers;
use App\Models\Cash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{

    public function index()
    {
        $customers = Customers::all();
        $cashAccounts = Cash::all();
        
        return view('dashboard.sales.pos', compact('customers', 'cashAccounts'));
    }

    public function getProducts(Request $request)
    {
        $query = Products::with(['category', 'productUnits.unit'])
            ->where('status_active', 1);
            
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('product_code', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->orderBy('name')->get();
        
        return response()->json($products);
    }

    public function getProduct($id)
    {
        $product = Products::with(['category', 'productUnits.unit', 'images'])
            ->findOrFail($id);
            
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cash_id' => 'required|exists:cash,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'percent_discount' => 'nullable|numeric|min:0|max:100',
            'amount_discount' => 'nullable|numeric|min:0',
            'input_payment' => 'required|numeric|min:0',
            'type_payment' => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        
        try {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . Str::upper(Str::random(4));
            
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            $percentDiscount = $request->percent_discount ?? 0;
            $amountDiscount = $request->amount_discount ?? 0;
            
            if ($percentDiscount > 0 && $amountDiscount == 0) {
                $amountDiscount = $subtotal * ($percentDiscount / 100);
            }
            
            $totalCost = $subtotal - $amountDiscount;
            $returnPayment = $request->input_payment - $totalCost;
            
            $order = Orders::create([
                'order_date' => now(),
                'order_number' => $orderNumber,
                'customer_id' => $request->customer_id,
                'cash_id' => $request->cash_id,
                'total_cost_before' => $subtotal,
                'percent_discount' => $percentDiscount,
                'amount_discount' => $amountDiscount,
                'input_payment' => $request->input_payment,
                'return_payment' => $returnPayment,
                'total_cost' => $totalCost,
                'status' => 'completed',
                'description' => $request->description,
                'type_payment' => $request->type_payment,
            ]);
            
            foreach ($request->items as $item) {
                $product = Products::findOrFail($item['product_id']);
                
                OrderItems::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'order_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
                
                $product->base_stock -= $item['quantity'];
                $product->sold += $item['quantity'];
                $product->save();
            }
            
            $cash = Cash::findOrFail($request->cash_id);
            $cash->amount += $totalCost;
            $cash->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id,
                'order_number' => $orderNumber,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Orders::with(['customer', 'cash', 'items.product', 'items.product.category'])
            ->findOrFail($id);
            
        return response()->json($order);
    }

    public function getOrdersData()
    {
        $orders = Orders::with(['customer', 'cash'])
            ->select('orders.*');
            
        return DataTables::of($orders)
            ->addColumn('no', function () {
                static $counter = 0;
                return ++$counter;
            })
            ->addColumn('order_date', function ($order) {
                return $order->order_date->format('d-m-Y H:i');
            })
            ->addColumn('customer_name', function ($order) {
                return $order->customer ? $order->customer->name : 'Umum';
            })
            ->addColumn('total_cost_formatted', function ($order) {
                return 'Rp ' . number_format($order->total_cost, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($order) {
                $badgeClass = $order->status == 'completed' ? 'success' : 'warning';
                return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($order->status) . '</span>';
            })
            ->addColumn('action', function ($order) {
                return '
                    <button class="btn btn-sm btn-info view-order-btn" data-id="' . $order->id . '">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary print-order-btn" data-id="' . $order->id . '">
                        <i class="fas fa-print"></i>
                    </button>
                ';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function printReceipt($id)
    {
        $order = Orders::with(['customer', 'cash', 'items.product'])
            ->findOrFail($id);
            
        $profile = \App\Models\Profiles::first();
            
        return view('dashboard.sales.receipt', compact('order', 'profile'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);
        
        $order = Orders::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }
}