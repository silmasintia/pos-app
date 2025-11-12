<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view-roles')->only(['index', 'data']);
    //     $this->middleware('permission:create-roles')->only(['store']);
    //     $this->middleware('permission:edit-roles')->only(['edit', 'update']);
    //     $this->middleware('permission:delete-roles')->only(['edit']);
    // }

    public function index()
    {
        $permissions = Permission::all();
        return view('access.roles', compact('permissions'));
    }

    public function data()
    {
        $roles = Role::withCount('users', 'permissions');
        
        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('action', function ($role) {
                return '
                    <button class="btn btn-sm btn-info edit-role" data-id="'.$role->id.'">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-role" data-id="'.$role->id.'">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
                'name' => 'required|unique:roles,name',
                'permissions' => 'array'
            ]);

            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
            
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            return response()->json(['success' => 'Role created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return response()->json($role);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Role not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|unique:roles,name,'.$id,
                'permissions' => 'array'
            ]);

            $role = Role::findOrFail($id);
            $role->update(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            return response()->json(['success' => 'Role updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            
            return response()->json(['success' => 'Role deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}