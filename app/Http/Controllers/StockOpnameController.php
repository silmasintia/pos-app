<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log; 

class StockOpnameController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;

        $this->middleware('permission:view-stock-opname')->only(['index', 'data', 'show', 'getProducts']);
        $this->middleware('permission:create-stock-opname')->only(['store']);
        $this->middleware('permission:edit-stock-opname')->only(['edit', 'update']);
        $this->middleware('permission:delete-stock-opname')->only(['destroy']);
    }

    public function index()
    {
        return view('opname.index');
    }

    public function data()
    {
        $stockOpnames = StockOpname::with(['details.product'])
            ->orderBy('opname_date', 'desc')
            ->orderBy('id', 'desc') 
            ->get();

        return DataTables::of($stockOpnames)
            ->addIndexColumn()
            ->addColumn('formatted_date', function ($stockOpname) {
                return date('d M Y', strtotime($stockOpname->opname_date));
            })
            ->addColumn('details_count', function ($stockOpname) {
                return $stockOpname->details->count();
            })
            ->addColumn('image_preview', function ($stockOpname) {
                if ($stockOpname->image) {
                    $imageUrl = asset('storage/' . $stockOpname->image);
                    return '<img src="' . $imageUrl . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">';
                }
                return '<span class="text-muted">No Image</span>';
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
            ->rawColumns(['action', 'image_preview'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'opname_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'required|array|min:1',
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
            $today = date('Ymd');
            $prefix = 'OPN-' . $today . '-';
            $lastOpnameToday = StockOpname::where('opname_number', 'LIKE', $prefix . '%')
                                          ->orderBy('id', 'desc')
                                          ->first();
            $lastNumber = $lastOpnameToday ? intval(substr($lastOpnameToday->opname_number, -4)) : 0;
            $opnameNumber = $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $stockOpname = new StockOpname();
            $stockOpname->opname_number = $opnameNumber;
            $stockOpname->opname_date = $request->opname_date;
            $stockOpname->description = $request->description;
            
            if ($request->hasFile('image')) {
                $stockOpname->image = $this->imageService->saveWebp($request->file('image'), 'stock_opname');
            }
            
            $stockOpname->save();

            foreach ($request->items as $item) {
                $product = Products::where('id', $item['product_id'])->lockForUpdate()->first();
                
                $detail = new StockOpnameDetail();
                $detail->stock_opname_id = $stockOpname->id;
                $detail->product_id = $item['product_id'];
                $detail->system_stock = $product->base_stock;
                $detail->physical_stock = $item['physical_stock'];
                $detail->difference = $item['physical_stock'] - $product->base_stock;
                $detail->description_detail = $item['description_detail'] ?? null;
                $detail->save();

                $product->base_stock = $item['physical_stock'];
                $product->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stock opname created successfully',
                'data' => $stockOpname
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating stock opname: ' . $e->getMessage());
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
            'items' => 'required|array|min:1', 
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
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Stock opname not found'
                ], 404);
            }

            $previousDetails = StockOpnameDetail::where('stock_opname_id', $id)->get();
            foreach ($previousDetails as $detail) {
                $product = Products::where('id', $detail->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->base_stock -= $detail->difference;
                    $product->save();
                }
            }

            $stockOpname->opname_date = $request->opname_date;
            $stockOpname->description = $request->description;
            
            if ($request->boolean('remove_image')) {
                if ($stockOpname->image) { 
                    $this->imageService->deleteFile($stockOpname->image);
                }
                $stockOpname->image = null;
            } 
            elseif ($request->hasFile('image')) {
                if ($stockOpname->image) {
                    $this->imageService->deleteFile($stockOpname->image);
                }
                $stockOpname->image = $this->imageService->saveWebp($request->file('image'), 'stock_opname');
            }
            
            $stockOpname->save();

            StockOpnameDetail::where('stock_opname_id', $id)->delete();

            foreach ($request->items as $item) {
                $product = Products::where('id', $item['product_id'])->lockForUpdate()->first();
                
                $detail = new StockOpnameDetail();
                $detail->stock_opname_id = $stockOpname->id;
                $detail->product_id = $item['product_id'];
                $detail->system_stock = $product->base_stock; 
                $detail->physical_stock = $item['physical_stock'];
                $detail->difference = $item['physical_stock'] - $product->base_stock;
                $detail->description_detail = $item['description_detail'] ?? null;
                $detail->save();

                $product->base_stock = $item['physical_stock'];
                $product->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stock opname updated successfully',
                'data' => $stockOpname
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating stock opname: ' . $e->getMessage());
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
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Stock opname not found'
                ], 404);
            }

            $details = StockOpnameDetail::where('stock_opname_id', $id)->get();
            foreach ($details as $detail) {
                $product = Products::where('id', $detail->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->base_stock -= $detail->difference;
                    $product->save();
                }
            }

            if ($stockOpname->image) {
                $this->imageService->deleteFile($stockOpname->image);
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
            Log::error('Error deleting stock opname: ' . $e->getMessage());
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