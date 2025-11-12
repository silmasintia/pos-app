<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{

    public function index()
    {
        return view('dashboard.product-management.categories');
    }

    public function data()
    {
        $categories = Categories::query()->orderBy('position', 'asc');

        return DataTables::of($categories)
            ->addColumn('no', function () {
                static $counter = 0;
                return ++$counter;
            })
            ->addColumn('action', function ($category) {
                return '
                    <button class="btn btn-sm btn-primary edit-btn" data-id="' . $category->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $category->id . '" data-name="' . $category->name . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->addColumn('image_preview', function ($category) {
                if ($category->image) {
                    return '<img src="' . asset($category->image) . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">';
                }
                return '<span class="text-muted">No Image</span>';
            })
            ->rawColumns(['action', 'image_preview'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'nullable|integer|min:0',
        ]);

        $position = $request->position ?? 0;
        
        $existingCategory = Categories::where('position', $position)->first();
        if ($existingCategory) {
            Categories::where('position', '>=', $position)->increment('position');
        }

        $category = new Categories();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->position = $position;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);
            $category->image = 'uploads/categories/' . $imageName;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.'
        ]);
    }

    public function edit($id)
    {
        $category = Categories::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Categories::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'nullable|integer|min:0',
        ]);

        $oldPosition = $category->position;
        $newPosition = $request->position ?? 0;

        if ($oldPosition != $newPosition) {
            $existingCategory = Categories::where('position', $newPosition)
                ->where('id', '!=', $category->id)
                ->first();
            
            if ($existingCategory) {
                if ($newPosition > $oldPosition) {
                    Categories::where('position', '>', $oldPosition)
                        ->where('position', '<=', $newPosition)
                        ->decrement('position');
                } else {
                    Categories::where('position', '>=', $newPosition)
                        ->where('position', '<', $oldPosition)
                        ->increment('position');
                }
            }
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->position = $newPosition;

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);
            $category->image = 'uploads/categories/' . $imageName;
        }

        if ($request->has('remove_image') && $request->remove_image) {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }
            $category->image = null;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $category = Categories::findOrFail($id);

        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category because it has associated products.'
            ], 422);
        }

        Categories::where('position', '>', $category->position)->decrement('position');

        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }
}