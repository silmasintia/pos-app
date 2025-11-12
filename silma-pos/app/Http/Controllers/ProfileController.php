<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil (view).
     */
    public function index()
    {
        $profile = Profiles::first();
        return view('dashboard.profile.index', compact('profile'));
    }

    /**
     * Ambil data profil (untuk AJAX).
     */
    public function data()
    {
        $profile = Profiles::first();

        return response()->json([
            'profile' => $profile,
            'social_medias' => $profile ? $profile->socialMedias : [],
        ]);
    }

    /**
     * Update atau buat profil (karena hanya 1 profil di sistem).
     */
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Ambil profil pertama, kalau belum ada buat baru
        $profile = Profiles::firstOrNew();
        $profile->fill($request->all());

        // === Upload file baru & hapus lama jika ada ===
        $fileFields = ['logo', 'logo_dark', 'favicon', 'banner', 'login_background'];

        foreach ($fileFields as $field) {
            // Check if remove checkbox is checked
            $removeField = 'remove_' . $field;
            if ($request->has($removeField) && $request->$removeField) {
                // Hapus file lama
                if (!empty($profile->$field)) {
                    Storage::disk('public')->delete($profile->$field);
                    $profile->$field = null;
                }
            } 
            // Check if new file is uploaded
            elseif ($request->hasFile($field)) {
                // Hapus file lama
                if (!empty($profile->$field)) {
                    Storage::disk('public')->delete($profile->$field);
                }

                // Simpan file baru
                $path = $request->file($field)->store('profile', 'public');
                $profile->$field = $path;
            }
        }

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }

    /**
     * Ambil profil berdasarkan ID (jika dibutuhkan).
     */
    public function show($id)
    {
        $profile = Profiles::findOrFail($id);

        return response()->json([
            'success' => true,
            'profile' => $profile,
        ]);
    }
}