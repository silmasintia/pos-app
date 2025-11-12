<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ImageService;

class CategoriesController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService) 
    {
        $this->imageService = $imageService;

        $this->middleware('permission:view-categories')->only(['index', 'data']);
        $this->middleware('permission:create-categories')->only(['create']);
        $this->middleware('permission:edit-categories')->only(['edit', 'update']);
        $this->middleware('permission:delete-categories')->only(['destroy']);
    }

    public function index()
    {
        return view('categories.index');
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
                    $imageUrl = asset('storage/' . $category->image);
                    return '<img src="' . $imageUrl . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">';
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
            $category->image = $this->imageService->saveWebp($request->file('image'), 'categories');
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
            $this->imageService->deleteFile($category->image);
            $category->image = $this->imageService->saveWebp($request->file('image'), 'categories');
        }

        if ($request->has('remove_image') && $request->remove_image) {
            $this->imageService->deleteFile($category->image);
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

        $this->imageService->deleteFile($category->image);

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }
}