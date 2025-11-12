<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('dashboard.users.list');
    }

    public function getUsersData(Request $request)
    {
        if ($request->ajax()) {
            $users = User::latest();
            
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('status', function($user) {
                    return '<span class="badge bg-'. ($user->status ? 'success' : 'danger') .'">
                                '. ($user->status ? 'Active' : 'Inactive') .'
                            </span>';
                })
                ->addColumn('action', function($user) {
                    return '<div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm edit-btn" 
                                    data-id="'. $user->id .'" data-bs-toggle="modal" 
                                    data-bs-target="#editUserModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                    data-id="'. $user->id .'">
                                    <span class="spinner-border spinner-border-sm text-danger d-none" 
                                        role="status" aria-hidden="true"></span>
                                    <span class="btn-text"><i class="fas fa-trash"></i></span>
                                </button>
                            </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        
        return abort(404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|unique:users,phone_number',
            'wa_number' => 'nullable|unique:users,wa_number',
            'password' => 'required|min:6',
            'address' => 'nullable|string',
            'about' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'status_display' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'wa_number' => $validated['wa_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'] ?? null,
            'about' => $validated['about'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'status_display' => $validated['status_display'],
        ];

        if ($request->hasFile('image')) {
            $userData['image'] = $request->file('image')->store('users/images', 'public');
        }

        if ($request->hasFile('banner')) {
            $userData['banner'] = $request->file('banner')->store('users/banners', 'public');
        }

        $user = User::create($userData);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'user' => $user
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:100',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|unique:users,phone_number,' . $id,
            'wa_number' => 'nullable|unique:users,wa_number,' . $id,
            'password' => 'nullable|min:6',
            'address' => 'nullable|string',
            'about' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'status_display' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'remove_banner' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'wa_number' => $validated['wa_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'about' => $validated['about'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'status_display' => $validated['status_display'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $updateData['image'] = $request->file('image')->store('users/images', 'public');
        } elseif (isset($validated['remove_image']) && $validated['remove_image']) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $updateData['image'] = null;
        }

        if ($request->hasFile('banner')) {
            if ($user->banner) {
                Storage::disk('public')->delete($user->banner);
            }
            $updateData['banner'] = $request->file('banner')->store('users/banners', 'public');
        } elseif (isset($validated['remove_banner']) && $validated['remove_banner']) {
            if ($user->banner) {
                Storage::disk('public')->delete($user->banner);
            }
            $updateData['banner'] = null;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'user' => $user
        ]);
        
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        if ($user->banner) {
            Storage::disk('public')->delete($user->banner);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
}