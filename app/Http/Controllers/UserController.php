<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageService; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService; 
     
        $this->middleware('permission:view-users')->only(['index', 'getUsersData']);
        $this->middleware('permission:create-users')->only(['store']);
        $this->middleware('permission:edit-users')->only(['edit', 'update']);
        $this->middleware('permission:delete-users')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    public function getUsersData(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->latest();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role', function ($user) {
                    if ($user->roles->count() > 0) {
                        $roleName = $user->roles->first()->name;
                        $badgeClass = match ($roleName) {
                            'admin' => 'bg-info',
                            'kasir' => 'bg-primary',
                            'gudang' => 'bg-success',
                            default => 'bg-secondary',
                        };
                        return '<span class="badge ' . $badgeClass . '">' . ucfirst($roleName) . '</span>';
                    }
                    return '<span class="badge bg-secondary">No Role</span>';
                })
                ->addColumn('status', fn($user) =>
                    '<span class="badge bg-' . ($user->status ? 'success' : 'danger') . '">' .
                    ($user->status ? 'Active' : 'Inactive') . '</span>'
                )
                ->addColumn('action', function ($user) {
                    $currentUserId = Auth::id();
                    $editButton = '
                        <button type="button" class="btn btn-primary btn-sm edit-btn" 
                            data-id="' . $user->id . '" data-bs-toggle="modal" 
                            data-bs-target="#editUserModal">
                            <i class="fas fa-edit"></i>
                        </button>';
                    $deleteButton = '';
                    if ($user->id != $currentUserId) {
                        $deleteButton = '
                            <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                data-id="' . $user->id . '">
                                <span class="spinner-border spinner-border-sm text-danger d-none" 
                                    role="status" aria-hidden="true"></span>
                                <span class="btn-text"><i class="fas fa-trash"></i></span>
                            </button>';
                    }
                    return '<div class="d-flex gap-2">' . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['role', 'status', 'action'])
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
            'role' => 'required|exists:roles,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = collect($validated)->only([
            'name', 'username', 'email', 'phone_number', 'wa_number',
            'address', 'about', 'description', 'status', 'status_display'
        ])->toArray();

        $userData['password'] = Hash::make($validated['password']);

        if ($request->hasFile('image')) {
            $userData['image'] = $this->imageService->saveWebp($request->file('image'), 'users/images');
        }

        if ($request->hasFile('banner')) {
            $userData['banner'] = $this->imageService->saveWebp($request->file('banner'), 'users/banners', '_banner');
        }

        $user = User::create($userData);
        $user->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'user' => $user
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $isEditingSelf = (Auth::id() == $user->id);

        $rules = [
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
            'role' => 'nullable|exists:roles,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
            'remove_banner' => 'nullable|boolean',
        ];

        if ($isEditingSelf) {
            $rules['role'] = 'nullable|string'; 
        }

        $validated = $request->validate($rules);

        $updateData = collect($validated)->only([
            'name', 'username', 'email', 'phone_number', 'wa_number',
            'address', 'about', 'description', 'status', 'status_display'
        ])->toArray();

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('image')) {
            $this->imageService->deleteFile($user->image);
            $updateData['image'] = $this->imageService->saveWebp($request->file('image'), 'users/images');
        } elseif (!empty($validated['remove_image'])) {
            $this->imageService->deleteFile($user->image);
            $updateData['image'] = null;
        }

        if ($request->hasFile('banner')) {
            $this->imageService->deleteFile($user->banner);
            $updateData['banner'] = $this->imageService->saveWebp($request->file('banner'), 'users/banners', '_banner');
        } elseif (!empty($validated['remove_banner'])) {
            $this->imageService->deleteFile($user->banner);
            $updateData['banner'] = null;
        }

        $user->update($updateData);

        if (!$isEditingSelf && !empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'user' => $user->fresh()
        ]);
    }

    public function destroy(string $id)
    {
        if (Auth::id() == $id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        $user = User::findOrFail($id);
        
        $this->imageService->deleteFile($user->image);
        $this->imageService->deleteFile($user->banner);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $user->role = $user->roles->first()?->name;
        return response()->json($user);
    }
}