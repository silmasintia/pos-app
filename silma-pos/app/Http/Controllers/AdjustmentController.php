<?php

namespace App\Http\Controllers;

use App\Models\Adjustments;
use App\Models\AdjustmentDetails;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdjustmentController extends Controller
{
    public function index()
    {
        return view('dashboard.stock-management.adjustment');
    }

    public function data()
    {
        $adjustments = Adjustments::with(['details.product'])
            ->orderBy('adjustment_date', 'desc')
            ->get();

        return DataTables::of($adjustments)
            ->addIndexColumn()
            ->addColumn('formatted_date', function ($adjustment) {
                return date('d M Y', strtotime($adjustment->adjustment_date));
            })
            ->addColumn('details_count', function ($adjustment) {
                return $adjustment->details->count(); 
            })
            ->addColumn('action', function ($adjustment) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $adjustment->id . '" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $adjustment->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $adjustment->id . '" data-number="' . $adjustment->adjustment_number . '" title="Delete">
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
            'adjustment_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255',
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
            $lastAdjustment = Adjustments::orderBy('id', 'desc')->first();
            $lastNumber = $lastAdjustment ? intval(substr($lastAdjustment->adjustment_number, -4)) : 0;
            $adjustmentNumber = 'ADJ-' . date('Ymd') . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $adjustment = new Adjustments();
            $adjustment->adjustment_number = $adjustmentNumber;
            $adjustment->adjustment_date = $request->adjustment_date;
            $adjustment->description = $request->description;
            
            $totalAdjustment = 0;
            foreach ($request->items as $item) {
                $totalAdjustment += $item['quantity'];
            }
            $adjustment->total = $totalAdjustment;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('uploads/adjustments');
                
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0755, true);
                }
                
                $image->move($imagePath, $imageName);
                $adjustment->image = 'uploads/adjustments/' . $imageName;
            }
            
            $adjustment->save();

            foreach ($request->items as $item) {
                $product = Products::find($item['product_id']);
                
                $detail = new AdjustmentDetails();
                $detail->adjustment_id = $adjustment->id;
                $detail->product_id = $item['product_id'];
                $detail->name = $product->name;
                $detail->product_code = $product->product_code;
                $detail->quantity = $item['quantity'];
                $detail->reason = $item['reason'] ?? null;
                $detail->save();
                
                $product->base_stock += $item['quantity'];
                $product->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Adjustment created successfully',
                'data' => $adjustment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating adjustment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating adjustment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $adjustment = Adjustments::with(['details.product'])->find($id);
        
        if (!$adjustment) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $adjustment
        ]);
    }

    public function edit($id)
    {
        $adjustment = Adjustments::with(['details.product'])->find($id);
        
        if (!$adjustment) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $adjustment
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'adjustment_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255',
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
            $adjustment = Adjustments::find($id);
            
            if (!$adjustment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment not found'
                ], 404);
            }

            $previousDetails = AdjustmentDetails::where('adjustment_id', $id)->get();
            foreach ($previousDetails as $detail) {
                $product = Products::find($detail->product_id);
                if ($product) {
                    $product->base_stock -= $detail->quantity;
                    $product->save();
                }
            }

            $adjustment->adjustment_date = $request->adjustment_date;
            $adjustment->description = $request->description;
            
            $totalAdjustment = 0;
            foreach ($request->items as $item) {
                $totalAdjustment += $item['quantity'];
            }
            $adjustment->total = $totalAdjustment;
            
            $removeImage = $request->input('remove_image') === '1';
            
            if ($removeImage) {
                if ($adjustment->image) {
                    $imagePath = public_path($adjustment->image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $adjustment->image = null;
                }
            } 
            elseif ($request->hasFile('image')) {
                if ($adjustment->image) {
                    $imagePath = public_path($adjustment->image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('uploads/adjustments');
                
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0755, true);
                }
                
                $image->move($imagePath, $imageName);
                $adjustment->image = 'uploads/adjustments/' . $imageName;
            }
            
            $adjustment->save();

            AdjustmentDetails::where('adjustment_id', $id)->delete();

            foreach ($request->items as $item) {
                $product = Products::find($item['product_id']);
                
                $detail = new AdjustmentDetails();
                $detail->adjustment_id = $adjustment->id;
                $detail->product_id = $item['product_id'];
                $detail->name = $product->name;
                $detail->product_code = $product->product_code;
                $detail->quantity = $item['quantity'];
                $detail->reason = $item['reason'] ?? null;
                $detail->save();
                
                $product->base_stock += $item['quantity'];
                $product->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Adjustment updated successfully',
                'data' => $adjustment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating adjustment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating adjustment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $adjustment = Adjustments::find($id);
            
            if (!$adjustment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment not found'
                ], 404);
            }

            $details = AdjustmentDetails::where('adjustment_id', $id)->get();
            foreach ($details as $detail) {
                $product = Products::find($detail->product_id);
                if ($product) {
                    $product->base_stock -= $detail->quantity;
                    $product->save();
                }
            }

            if ($adjustment->image && file_exists(public_path($adjustment->image))) {
                unlink(public_path($adjustment->image));
            }

            AdjustmentDetails::where('adjustment_id', $id)->delete();

            $adjustment->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Adjustment deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting adjustment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting adjustment: ' . $e->getMessage()
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