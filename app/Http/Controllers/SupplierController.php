<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-suppliers')->only(['index', 'getSuppliersData']);
        $this->middleware('permission:create-suppliers')->only(['store']);
        $this->middleware('permission:edit-suppliers')->only(['edit', 'update']);
        $this->middleware('permission:delete-suppliers')->only(['destroy']);
    }

    public function index()
    {
        return view('suppliers.index');
    }

    public function getSuppliersData()
    {
        $suppliers = Suppliers::latest();

        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('action', function($supplier) {
                $btn = '<button class="btn btn-sm btn-primary edit-btn me-1" data-id="'.$supplier->id.'"><i class="fas fa-edit"></i></button>';
                $btn .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$supplier->id.'"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $supplier = Suppliers::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully',
            'data' => $supplier
        ]);
    }

    public function edit($id)
    {
        $supplier = Suppliers::find($id);
        
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        return response()->json($supplier);
    }

    public function update(Request $request, $id)
    {
        $supplier = Suppliers::find($id);
        
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $supplier->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully',
            'data' => $supplier
        ]);
    }

    public function destroy($id)
    {
        $supplier = Suppliers::find($id);
        
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully'
        ]);
    }
}