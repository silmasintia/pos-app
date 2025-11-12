<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use App\Models\Suppliers;
use App\Models\Cash;
use App\Models\PurchaseItems;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function index()
    {
        $suppliers = Suppliers::all();
        $cashAccounts = Cash::all();
        return view('dashboard.purchases', compact('suppliers', 'cashAccounts'));
    }

    public function getPurchasesData()
    {
        $purchases = Purchases::with(['supplier', 'cash'])->select('purchases.*');

        return DataTables::of($purchases)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($purchase) {
                return $purchase->supplier ? $purchase->supplier->name : '-';
            })
            ->addColumn('cash_name', function ($purchase) {
                return $purchase->cash ? $purchase->cash->name : '-';
            })
            ->addColumn('formatted_date', function ($purchase) {
                return date('d-m-Y', strtotime($purchase->purchase_date));
            })
            ->addColumn('formatted_total', function ($purchase) {
                return 'Rp ' . number_format($purchase->total_cost, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($purchase) {
                $statusClass = '';
                $statusText = '';

                switch ($purchase->status) {
                    case 'completed':
                        $statusClass = 'bg-success';
                        $statusText = 'Completed';
                        break;
                    case 'pending':
                        $statusClass = 'bg-warning';
                        $statusText = 'Pending';
                        break;
                    case 'cancelled':
                        $statusClass = 'bg-danger';
                        $statusText = 'Cancelled';
                        break;
                    default:
                        $statusClass = 'bg-secondary';
                        $statusText = ucfirst($purchase->status);
                }

                return '<span class="badge ' . $statusClass . '">' . $statusText . '</span>';
            })
            ->addColumn('action', function ($purchase) {
                $viewBtn = '<button class="btn btn-sm btn-info view-btn" data-id="' . $purchase->id . '" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>';
                $editBtn = '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $purchase->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>';
                $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $purchase->id . '" data-number="' . $purchase->purchase_number . '" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>';

                return '<div class="btn-group">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'purchase_date' => 'required|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'cash_id' => 'required|exists:cash,id',
                'description' => 'nullable|string',
                'type_payment' => 'required|string',
                'status' => 'required|string',
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.purchase_price' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $purchaseNumber = 'PUR-' . date('YmdHis');

            $purchase = new Purchases();
            $purchase->purchase_date = $request->purchase_date;
            $purchase->purchase_number = $purchaseNumber;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->cash_id = $request->cash_id;
            $purchase->description = $request->description;
            $purchase->type_payment = $request->type_payment;
            $purchase->status = $request->status;

            $totalCost = 0;
            foreach ($request->items as $item) {
                $totalCost += $item['quantity'] * $item['purchase_price'];
            }
            $purchase->total_cost = $totalCost;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/purchases'), $imageName);
                $purchase->image = 'uploads/purchases/' . $imageName;
            }

            $purchase->save();

            foreach ($request->items as $item) {
                $purchaseItem = new PurchaseItems();
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->purchase_price = $item['purchase_price'];
                $purchaseItem->total_price = $item['quantity'] * $item['purchase_price'];
                $purchaseItem->save();

                $product = Products::find($item['product_id']);
                if ($product) {
                    $product->base_stock += $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase created successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $purchase = Purchases::with(['supplier', 'cash', 'items.product'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $purchase
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found: ' . $e->getMessage()
            ], 404);
        }
    }

    public function edit($id)
    {
        try {
            $purchase = Purchases::with(['items.product'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $purchase
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found: ' . $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'purchase_date' => 'required|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'cash_id' => 'required|exists:cash,id',
                'description' => 'nullable|string',
                'type_payment' => 'required|string',
                'status' => 'required|string',
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.purchase_price' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $purchase = Purchases::findOrFail($id);

            foreach ($purchase->items as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $product->base_stock -= $item->quantity;
                    $product->save();
                }
            }

            $purchase->purchase_date = $request->purchase_date;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->cash_id = $request->cash_id;
            $purchase->description = $request->description;
            $purchase->type_payment = $request->type_payment;
            $purchase->status = $request->status;

            $totalCost = 0;
            foreach ($request->items as $item) {
                $totalCost += $item['quantity'] * $item['purchase_price'];
            }
            $purchase->total_cost = $totalCost;

            if ($request->has('remove_image') && $request->remove_image) {
                if ($purchase->image && file_exists(public_path($purchase->image))) {
                    unlink(public_path($purchase->image));
                }
                $purchase->image = null;
            } else if ($request->hasFile('image')) {
                if ($purchase->image && file_exists(public_path($purchase->image))) {
                    unlink(public_path($purchase->image));
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/purchases'), $imageName);
                $purchase->image = 'uploads/purchases/' . $imageName;
            }

            $purchase->save();

            PurchaseItems::where('purchase_id', $purchase->id)->delete();

            foreach ($request->items as $item) {
                $purchaseItem = new PurchaseItems();
                $purchaseItem->purchase_id = $purchase->id;
                $purchaseItem->product_id = $item['product_id'];
                $purchaseItem->quantity = $item['quantity'];
                $purchaseItem->purchase_price = $item['purchase_price'];
                $purchaseItem->total_price = $item['quantity'] * $item['purchase_price'];
                $purchaseItem->save();

                $product = Products::find($item['product_id']);
                if ($product) {
                    $product->base_stock += $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $purchase = Purchases::findOrFail($id);

            foreach ($purchase->items as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $product->base_stock -= $item->quantity;
                    $product->save();
                }
            }

            if ($purchase->image && file_exists(public_path($purchase->image))) {
                unlink(public_path($purchase->image));
            }

            PurchaseItems::where('purchase_id', $purchase->id)->delete();

            $purchase->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProducts()
    {
        try {
            $products = Products::with(['category', 'baseUnit'])
                ->where('status_active', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading products: ' . $e->getMessage()
            ], 500);
        }
    }
}