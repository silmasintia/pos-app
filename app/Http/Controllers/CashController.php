<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CashController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-cash')->only(['index', 'data']);
        $this->middleware('permission:create-cash')->only(['store']);
        $this->middleware('permission:edit-cash')->only(['edit', 'update']);
        $this->middleware('permission:delete-cash')->only(['destroy']);
    }

    public function index()
    {
        return view('cashes.index');
    }

    public function data()
    {
        $cash = Cash::query();

        return DataTables::of($cash)
            ->addColumn('no', function () {
                static $counter = 0;
                return ++$counter;
            })
            ->addColumn('amount_formatted', function ($cash) {
                return 'Rp ' . number_format($cash->amount, 2, ',', '.');
            })
            ->addColumn('action', function ($cash) {
                return '
                    <button class="btn btn-sm btn-info edit-btn" data-id="' . $cash->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $cash->id . '" data-name="' . $cash->name . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cash',
            'amount' => 'required|numeric|min:0',
        ]);

        $cash = new Cash();
        $cash->name = $request->name;
        $cash->amount = $request->amount;
        $cash->save();

        return response()->json([
            'success' => true,
            'message' => 'Cash account created successfully.'
        ]);
    }

    public function edit($id)
    {
        $cash = Cash::findOrFail($id);
        return response()->json($cash);
    }

    public function update(Request $request, $id)
    {
        $cash = Cash::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:cash,name,' . $cash->id,
            'amount' => 'required|numeric|min:0',
        ]);

        $cash->name = $request->name;
        $cash->amount = $request->amount;
        $cash->save();

        return response()->json([
            'success' => true,
            'message' => 'Cash account updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $cash = Cash::findOrFail($id);

        if ($cash->transactions()->count() > 0 || $cash->orders()->count() > 0 || $cash->purchases()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete cash account because it has associated transactions, orders, or purchases.'
            ], 422);
        }

        $cash->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cash account deleted successfully.'
        ]);
    }
}