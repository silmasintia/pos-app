<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Categories;
use App\Models\ProductUnits;
use App\Models\Units;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function index()
    {
        $categories = Categories::all();
        $units = Units::all();
        return view('dashboard.product-management.products', compact('categories', 'units'));
    }

    public function getProducts(Request $request)
    {
        if ($request->ajax()) {
            $data = Products::with(['category', 'baseUnit', 'productUnits' => function ($query) {
                $query->where('is_base', true);
            }])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('no', function ($row) {
                    static $counter = 0;
                    return ++$counter;
                })
                ->addColumn('image_preview', function ($row) {
                    $imagePath = $row->image
                        ? asset('storage/' . $row->image)
                        : 'https://via.placeholder.com/50x50?text=No+Image';
                    return '<img src="' . $imagePath . '" alt="' . $row->name . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
                })
                ->addColumn('category_name', function ($row) {
                    return $row->category ? $row->category->name : '-';
                })
                ->addColumn('base_unit_name', function ($row) {
                    return $row->baseUnit ? $row->baseUnit->name : '-';
                })
                ->addColumn('purchase_price', function ($row) {
                    $baseUnit = $row->productUnits->first();
                    return $baseUnit ? number_format($baseUnit->purchase_price, 2) : '0.00';
                })
                ->addColumn('cost_price', function ($row) {
                    $baseUnit = $row->productUnits->first();
                    return $baseUnit ? number_format($baseUnit->cost_price, 2) : '0.00';
                })
                ->addColumn('price_before_discount', function ($row) {
                    $baseUnit = $row->productUnits->first();
                    return $baseUnit ? number_format($baseUnit->price_before_discount, 2) : '0.00';
                })
                ->addColumn('status', function ($row) {
                    $statusHtml = '';
                    $statusHtml .= $row->status_active ?
                        '<span class="badge bg-success">Active</span> ' :
                        '<span class="badge bg-danger">Inactive</span> ';
                    $statusHtml .= $row->status_discount ?
                        '<span class="badge bg-warning">Discount</span>' :
                        '<span class="badge bg-secondary">No Discount</span>';
                    return $statusHtml;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '"><i class="fas fa-edit"></i></button>';
                    $btn .= ' <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" data-name="' . $row->name . '"><i class="fas fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['image_preview', 'status', 'action'])
                ->make(true);
        }
        return abort(404);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_code' => 'required|unique:products,product_code',
                'name' => 'required',
                'category_id' => 'required|exists:categories,id',
                'base_unit_id' => 'required|exists:units,id',
                'base_stock' => 'required|integer|min:0',
                'purchase_price' => 'required|numeric|min:0',
                'cost_price' => 'required|numeric|min:0',
                'price_before_discount' => 'required|numeric|min:0',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$request->filled('position')) {
                $maxPosition = Products::max('position');
                $position = $maxPosition ? $maxPosition + 1 : 1;
            } else {
                $position = $request->position;
            }

            $product = new Products();
            $product->product_code = $request->product_code;
            $product->barcode = $request->barcode;
            $product->name = $request->name;
            $product->slug = Str::slug($request->name);
            $product->category_id = $request->category_id;
            $product->description = $request->description;
            $product->status_active = $request->has('status_active') ? 1 : 0;
            $product->status_discount = $request->has('status_discount') ? 1 : 0;
            $product->status_display = $request->has('status_display') ? 1 : 0;
            $product->note = $request->note;
            $product->position = $position;
            $product->reminder = $request->reminder;
            $product->link = $request->link;
            $product->expire_date = $request->expire_date;
            $product->sold = 0;
            $product->base_unit_id = $request->base_unit_id;
            $product->base_stock = $request->base_stock;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
            }

            $product->save();

            $productUnit = new ProductUnits();
            $productUnit->product_id = $product->id;
            $productUnit->unit_id = $request->base_unit_id;
            $productUnit->conversion_factor = 1;
            $productUnit->purchase_price = $request->purchase_price;
            $productUnit->cost_price = $request->cost_price;
            $productUnit->price_before_discount = $request->price_before_discount;
            $productUnit->is_base = true;
            $productUnit->note = $request->unit_note;
            $productUnit->save();

            return response()->json(['success' => true, 'message' => 'Product created successfully.']);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $product = Products::with(['category', 'baseUnit', 'productUnits'])
                ->findOrFail($id);

            $baseUnit = $product->productUnits->where('is_base', 1)->first();

            $product->base_unit_prices = $baseUnit ? [
                'purchase_price' => $baseUnit->purchase_price,
                'cost_price' => $baseUnit->cost_price,
                'price_before_discount' => $baseUnit->price_before_discount,
                'note' => $baseUnit->note,
            ] : null;

            return response()->json($product);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading product: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Products::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'product_code' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products', 'product_code')->ignore($id),
                ],
                'name' => 'required',
                'category_id' => 'required|exists:categories,id',
                'base_unit_id' => 'required|exists:units,id',
                'base_stock' => 'required|integer|min:0',
                'purchase_price' => 'required|numeric|min:0',
                'cost_price' => 'required|numeric|min:0',
                'price_before_discount' => 'required|numeric|min:0',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$request->filled('position')) {
                $maxPosition = Products::where('id', '!=', $id)->max('position');
                $position = $maxPosition ? $maxPosition + 1 : 1;
            } else {
                $position = $request->position;
            }

            $product->product_code = $request->product_code;
            $product->barcode = $request->barcode;
            $product->name = $request->name;
            $product->slug = Str::slug($request->name);
            $product->category_id = $request->category_id;
            $product->description = $request->description;
            $product->status_active = $request->has('status_active') ? 1 : 0;
            $product->status_discount = $request->has('status_discount') ? 1 : 0;
            $product->status_display = $request->has('status_display') ? 1 : 0;
            $product->note = $request->note;
            $product->position = $position;
            $product->reminder = $request->reminder;
            $product->link = $request->link;
            $product->expire_date = $request->expire_date;
            $product->base_unit_id = $request->base_unit_id;
            $product->base_stock = $request->base_stock;

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
            } elseif ($request->has('remove_image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                    $product->image = null;
                }
            }

            $product->save();

            $baseUnit = $product->productUnits()->where('is_base', true)->first();
            if ($baseUnit) {
                $baseUnit->unit_id = $request->base_unit_id;
                $baseUnit->purchase_price = $request->purchase_price;
                $baseUnit->cost_price = $request->cost_price;
                $baseUnit->price_before_discount = $request->price_before_discount;
                $baseUnit->note = $request->unit_note;
                $baseUnit->is_base = true;
                $baseUnit->save();
            } else {
                $baseUnit = new ProductUnits();
                $baseUnit->product_id = $product->id;
                $baseUnit->unit_id = $request->base_unit_id;
                $baseUnit->conversion_factor = 1;
                $baseUnit->purchase_price = $request->purchase_price;
                $baseUnit->cost_price = $request->cost_price;
                $baseUnit->price_before_discount = $request->price_before_discount;
                $baseUnit->is_base = true;
                $baseUnit->note = $request->unit_note;
                $baseUnit->save();
            }

            return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Products::findOrFail($id);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->productUnits()->delete();
            $product->delete();

            return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }
}
