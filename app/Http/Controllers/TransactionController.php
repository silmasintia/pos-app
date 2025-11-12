<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\Transactions;
use App\Models\TransactionCategories;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ImageService; 

class TransactionController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;

        $this->middleware('permission:view-transactions')->only(['index', 'data']);
        $this->middleware('permission:create-transactions')->only(['store']);
        $this->middleware('permission:edit-transactions')->only(['edit', 'update']);
        $this->middleware('permission:delete-transactions')->only(['destroy']);
    }

    public function index()
    {
        $cashAccounts = Cash::all();
        $categories = TransactionCategories::all();
        return view('transactions.index', compact('cashAccounts', 'categories'));
    }

    public function data()
    {
        $transactions = Transactions::with(['cash', 'category']);

        return DataTables::of($transactions)
            ->addColumn('no', function () {
                static $counter = 0;
                return ++$counter;
            })
            ->addColumn('date_formatted', function ($transaction) {
                return date('d-m-Y', strtotime($transaction->date));
            })
            ->addColumn('amount_formatted', function ($transaction) {
                $amount = $transaction->amount;
                $class = $amount >= 0 ? 'text-success' : 'text-danger';
                $prefix = $amount >= 0 ? '+' : '';
                return '<span class="' . $class . '">Rp ' . number_format(abs($amount), 2, ',', '.') . '</span>';
            })
            ->addColumn('cash_name', function ($transaction) {
                return $transaction->cash ? $transaction->cash->name : '-';
            })
            ->addColumn('category_name', function ($transaction) {
                return $transaction->category ? $transaction->category->name : '-';
            })
            ->addColumn('image_preview', function ($transaction) {
                if ($transaction->image) {
                    $imageUrl = asset('storage/' . $transaction->image);
                    return '<img src="' . $imageUrl . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">';
                }
                return '<span class="text-muted">No Image</span>';
            })
            ->addColumn('action', function ($transaction) {
                return '
                    <button class="btn btn-sm btn-info edit-btn" data-id="' . $transaction->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $transaction->id . '" data-name="' . $transaction->name . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['amount_formatted', 'image_preview', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'cash_id' => 'required|exists:cash,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction = new Transactions();
        $transaction->date = $request->date;
        $transaction->transaction_category_id = $request->transaction_category_id;
        $transaction->cash_id = $request->cash_id;
        $transaction->name = $request->name;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;

        if ($request->hasFile('image')) {
            $transaction->image = $this->imageService->saveWebp($request->file('image'), 'transactions');
        }

        $transaction->save();

        $cash = Cash::find($request->cash_id);
        $cash->amount += $transaction->amount;
        $cash->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully.'
        ]);
    }

    public function edit($id)
    {
        $transaction = Transactions::with(['cash', 'category'])->findOrFail($id);
        
        $transaction->date = date('Y-m-d', strtotime($transaction->date));
        
        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transactions::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'cash_id' => 'required|exists:cash,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $originalAmount = $transaction->amount;
        $originalCashId = $transaction->cash_id;

        $transaction->date = $request->date;
        $transaction->transaction_category_id = $request->transaction_category_id;
        $transaction->cash_id = $request->cash_id;
        $transaction->name = $request->name;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;

        if ($request->hasFile('image')) {
            $this->imageService->deleteFile($transaction->image);
            $transaction->image = $this->imageService->saveWebp($request->file('image'), 'transactions');
        }

        if ($request->has('remove_image') && $request->remove_image) {
            $this->imageService->deleteFile($transaction->image);
            $transaction->image = null;
        }

        $transaction->save();

        if ($originalCashId != $request->cash_id) {
            $originalCash = Cash::find($originalCashId);
            $originalCash->amount -= $originalAmount;
            $originalCash->save();

            $newCash = Cash::find($request->cash_id);
            $newCash->amount += $transaction->amount;
            $newCash->save();
        } else {
            $difference = $transaction->amount - $originalAmount;
            $cash = Cash::find($request->cash_id);
            $cash->amount += $difference;
            $cash->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $transaction = Transactions::findOrFail($id);

        $cash = Cash::find($transaction->cash_id);
        $cash->amount -= $transaction->amount;
        $cash->save();

        $this->imageService->deleteFile($transaction->image);

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully.'
        ]);
    }
}