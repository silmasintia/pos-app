<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view-permissions')->only(['index', 'data']);
    //     $this->middleware('permission:create-permissions')->only(['store']);
    //     $this->middleware('permission:edit-permissions')->only(['edit', 'update']);
    //     $this->middleware('permission:delete-permissions')->only(['destroy']);
    // }

    public function index()
    {
        return view('access.permissions');
    }

    public function data()
    {
        $permissions = Permission::withCount('roles', 'users');

        return DataTables::of($permissions)
            ->addIndexColumn()
            ->addColumn('action', function ($permission) {
                return '
                <button class="btn btn-sm btn-info edit-permission" data-id="' . $permission->id . '">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-permission" data-id="' . $permission->id . '">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:permissions,name'
            ]);

            Permission::create(['name' => $request->name, 'guard_name' => 'web']);

            return response()->json(['success' => 'Permission created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return response()->json($permission);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Permission not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|unique:permissions,name,' . $id
            ]);

            $permission = Permission::findOrFail($id);
            $permission->update(['name' => $request->name]);

            return response()->json(['success' => 'Permission updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json(['success' => 'Permission deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}