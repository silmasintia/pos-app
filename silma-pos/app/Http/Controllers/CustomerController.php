<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\CustomerCategories;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        $categories = CustomerCategories::all();
        return view('dashboard.contacts.customers', compact('categories'));
    }

    public function getCustomersData()
    {
        $customers = Customers::with('category')->latest();

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('category_name', function ($customer) {
                if (!$customer->category) {
                    return '<span class="badge bg-secondary">No Category</span>';
                }

                $name = $customer->category->name;
                $colorClass = match (strtolower($name)) {
                    'umum' => 'bg-secondary',
                    'member' => 'bg-info',
                    'vip' => 'bg-warning',
                    default => 'bg-light text-dark',
                };

                return '<span class="badge ' . $colorClass . '">' . e($name) . '</span>';
            })

            ->addColumn('action', function ($customer) {
                $btn = '
                    <button class="btn btn-sm btn-primary edit-btn me-1" data-id="' . $customer->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $customer->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                return $btn;
            })
            ->rawColumns(['action', 'category_name'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'customer_category_id' => 'nullable|exists:customer_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customerData = $request->all();
        if (empty($customerData['customer_category_id'])) {
            $customerData['customer_category_id'] = 1;
        }

        $customer = Customers::create($customerData);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $customer
        ]);
    }

    public function edit($id)
    {
        $customer = Customers::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customers::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'customer_category_id' => 'nullable|exists:customer_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customerData = $request->all();
        if (empty($customerData['customer_category_id'])) {
            $customerData['customer_category_id'] = 1;
        }

        $customer->update($customerData);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer
        ]);
    }

    public function destroy($id)
    {
        $customer = Customers::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully'
        ]);
    }
}
