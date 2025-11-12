<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialMedias;
use App\Models\Profiles;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SocialMediaController extends Controller
{
    public function index()
    {
        return view('dashboard.social-media.index');
    }

    public function data()
    {
        $profile = Profiles::first();
        
        if (!$profile) {
            return DataTables::of([])->make(true);
        }
        
        $query = SocialMedias::where('profile_id', $profile->id);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('image_preview', function ($social) {
                if ($social->image) {
                    return '<img src="' . asset('storage/' . $social->image) . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;" alt="' . $social->name . '">';
                }
                return '<span class="badge bg-secondary">No Image</span>';
            })
            ->addColumn('action', function ($social) {
                return '<button class="btn btn-sm btn-info edit-social-btn" data-id="'.$social->id.'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-social-btn" data-id="'.$social->id.'">
                            <i class="fas fa-trash"></i>
                        </button>';
            })
            ->rawColumns(['image_preview', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $profile = Profiles::first();
        
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ], 404);
        }

        $socialMedia = new SocialMedias();
        $socialMedia->profile_id = $profile->id;
        $socialMedia->fill($request->except(['image']));
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('social_media', 'public');
            $socialMedia->image = $path;
        }
        
        $socialMedia->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Social media created successfully',
            'social_media' => $socialMedia
        ]);
    }

    public function edit($id)
    {
        $socialMedia = SocialMedias::find($id);
        
        if (!$socialMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Social media not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'social_media' => $socialMedia
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $socialMedia = SocialMedias::find($id);
        
        if (!$socialMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Social media not found'
            ], 404);
        }
        
        $socialMedia->fill($request->except(['image']));
        
        if ($request->hasFile('image')) {
            if ($socialMedia->image && Storage::disk('public')->exists($socialMedia->image)) {
                Storage::disk('public')->delete($socialMedia->image);
            }
            
            $path = $request->file('image')->store('social_media', 'public');
            $socialMedia->image = $path;
        }
        
        if ($request->has('remove_image') && $request->remove_image) {
            if ($socialMedia->image && Storage::disk('public')->exists($socialMedia->image)) {
                Storage::disk('public')->delete($socialMedia->image);
            }
            $socialMedia->image = null;
        }
        
        $socialMedia->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Social media updated successfully',
            'social_media' => $socialMedia
        ]);
    }

    public function show($id)
    {
        $socialMedia = SocialMedias::find($id);
        
        if (!$socialMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Social media not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'social_media' => $socialMedia
        ]);
    }

    public function delete($id)
    {
        $socialMedia = SocialMedias::find($id);
        
        if (!$socialMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Social media not found'
            ], 404);
        }
        
        if ($socialMedia->image && Storage::disk('public')->exists($socialMedia->image)) {
            Storage::disk('public')->delete($socialMedia->image);
        }
        
        $socialMedia->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Social media deleted successfully'
        ]);
    }
}