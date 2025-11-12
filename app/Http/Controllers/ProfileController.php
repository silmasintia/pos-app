<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageService;

class ProfileController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService; 

        $this->middleware('permission:view-profile')->only(['index', 'data', 'show']);
        $this->middleware('permission:edit-profile')->only(['update']);
    }
    
    public function index()
    {
        $profile = Profiles::first();
        return view('compro.profile', compact('profile'));
    }

    public function data()
    {
        $profile = Profiles::first();

        return response()->json([
            'profile' => $profile,
            'social_medias' => $profile ? $profile->socialMedias : [],
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'identity_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description_1' => 'nullable|string',
            'description_2' => 'nullable|string',
            'description_3' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'theme_color' => 'nullable|string|max:255',
            'boxed_layout' => 'nullable|boolean',
            'sidebar_type' => 'nullable|string|max:255',
            'card_border' => 'nullable|boolean',
            'direction' => 'nullable|string|max:10',
            'keyword' => 'nullable|string|max:255',
            'keyword_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'login_background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_logo' => 'nullable|boolean',
            'remove_logo_dark' => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
            'remove_banner' => 'nullable|boolean',
            'remove_login_background' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $profile = Profiles::firstOrNew();
        $profile->fill($request->except(['logo', 'logo_dark', 'favicon', 'banner', 'login_background', 'remove_logo', 'remove_logo_dark', 'remove_favicon', 'remove_banner', 'remove_login_background']));

        $fileFields = ['logo', 'logo_dark', 'favicon', 'banner', 'login_background'];

        foreach ($fileFields as $field) {
            $removeField = 'remove_' . $field;

            if ($request->boolean($removeField)) {
                if (!empty($profile->$field)) {
                    $this->imageService->deleteFile($profile->$field);
                    $profile->$field = null;
                }
            } 
            elseif ($request->hasFile($field)) {
                if (!empty($profile->$field)) {
                    $this->imageService->deleteFile($profile->$field);
                }

                $profile->$field = $this->imageService->saveWebp($request->file($field), 'profile', '_' . $field);
            }
        }

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }

    public function show($id)
    {
        $profile = Profiles::findOrFail($id);

        return response()->json([
            'success' => true,
            'profile' => $profile,
        ]);
    }
}