<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    public function index()
    {
        return view('dashboard.stock-management.stock-opname');
    }

    public function data()
    {
        $stockOpnames = StockOpname::with(['details.product'])
            ->orderBy('opname_date', 'desc')
            ->get();

        return DataTables::of($stockOpnames)
            ->addIndexColumn()
            ->addColumn('formatted_date', function ($stockOpname) {
                return date('d M Y', strtotime($stockOpname->opname_date));
            })
            ->addColumn('details_count', function ($stockOpname) {
                return $stockOpname->details->count();
            })
            ->addColumn('action', function ($stockOpname) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $stockOpname->id . '" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $stockOpname->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $stockOpname->id . '" data-number="' . $stockOpname->opname_number . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'opname_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.physical_stock' => 'required|numeric|min:0',
            'items.*.description_detail' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $lastOpname = StockOpname::orderBy('id', 'desc')->first();
            $lastNumber = $lastOpname ? intval(substr($lastOpname->opname_number, -4)) : 0;
            $opnameNumber = 'OPN-' . date('Ymd') . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $stockOpname = new StockOpname();
            $stockOpname->opname_number = $opnameNumber;
            $stockOpname->opname_date = $request->opname_date;
            $stockOpname->description = $request->description;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/stock_opname'), $imageName);
                $stockOpname->image = 'uploads/stock_opname/' . $imageName;
            }
            
            $stockOpname->save();

            foreach ($request->items as $item) {
                $product = Products::find($item['product_id']);
                
                $detail = new StockOpnameDetail();
                $detail->stock_opname_id = $stockOpname->id;
                $detail->product_id = $item['product_id'];
                $detail->system_stock = $product->base_stock;
                $detail->physical_stock = $item['physical_stock'];
                $detail->difference = $item['physical_stock'] - $product->base_stock;
                $detail->description_detail = $item['description_detail'] ?? null;
                $detail->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stock opname created successfully',
                'data' => $stockOpname
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating stock opname: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $stockOpname = StockOpname::with(['details.product'])->find($id);
        
        if (!$stockOpname) {
            return response()->json([
                'success' => false,
                'message' => 'Stock opname not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stockOpname
        ]);
    }

    public function edit($id)
    {
        $stockOpname = StockOpname::with(['details.product'])->find($id);
        
        if (!$stockOpname) {
            return response()->json([
                'success' => false,
                'message' => 'Stock opname not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stockOpname
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'opname_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.physical_stock' => 'required|numeric|min:0',
            'items.*.description_detail' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $stockOpname = StockOpname::find($id);
            
            if (!$stockOpname) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock opname not found'
                ], 404);
            }

            $stockOpname->opname_date = $request->opname_date;
            $stockOpname->description = $request->description;
            
            if ($request->boolean('remove_image')) {
                if ($stockOpname->image && file_exists(public_path($stockOpname->image))) {
                    unlink(public_path($stockOpname->image));
                }
                $stockOpname->image = null;
            } 
            elseif ($request->hasFile('image')) {
                if ($stockOpname->image && file_exists(public_path($stockOpname->image))) {
                    unlink(public_path($stockOpname->image));
                }
                
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/stock_opname'), $imageName);
                $stockOpname->image = 'uploads/stock_opname/' . $imageName;
            }
            
            $stockOpname->save();

            StockOpnameDetail::where('stock_opname_id', $id)->delete();

            foreach ($request->items as $item) {
                $product = Products::find($item['product_id']);
                
                $detail = new StockOpnameDetail();
                $detail->stock_opname_id = $stockOpname->id;
                $detail->product_id = $item['product_id'];
                $detail->system_stock = $product->base_stock;
                $detail->physical_stock = $item['physical_stock'];
                $detail->difference = $item['physical_stock'] - $product->base_stock;
                $detail->description_detail = $item['description_detail'] ?? null;
                $detail->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stock opname updated successfully',
                'data' => $stockOpname
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating stock opname: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $stockOpname = StockOpname::find($id);
            
            if (!$stockOpname) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock opname not found'
                ], 404);
            }

            if ($stockOpname->image && file_exists(public_path($stockOpname->image))) {
                unlink(public_path($stockOpname->image));
            }

            StockOpnameDetail::where('stock_opname_id', $id)->delete();

            $stockOpname->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stock opname deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting stock opname: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProducts()
    {
        $products = Products::with(['category'])
            ->where('status_active', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}