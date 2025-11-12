@extends('layouts.app')

@section('title', 'Profile Management')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Profile Management</h4>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General Information</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab" aria-controls="appearance" aria-selected="false">Appearance</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab" aria-controls="seo" aria-selected="false">SEO</button>
                    </li>
                </ul>
                <div class="tab-content mt-4" id="profileTabsContent">
                    <!-- General Information Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <form id="profile-form">
                            <input type="hidden" name="id" id="profile-id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profile_name" class="form-label">Profile Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="profile_name" name="profile_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alias" class="form-label">Alias</label>
                                        <input type="text" class="form-control" id="alias" name="alias">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="identity_number" class="form-label">Identity Number</label>
                                        <input type="text" class="form-control" id="identity_number" name="identity_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="phone_number" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                        <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="text" class="form-control" id="website" name="website">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description_1" class="form-label">Description 1</label>
                                <textarea class="form-control" id="description_1" name="description_1" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="description_2" class="form-label">Description 2</label>
                                <textarea class="form-control" id="description_2" name="description_2" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="description_3" class="form-label">Description 3</label>
                                <textarea class="form-control" id="description_3" name="description_3" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Appearance Tab -->
                    <div class="tab-pane fade" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
                        <form id="appearance-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="theme" class="form-label">Theme</label>
                                        <select class="form-select" id="theme" name="theme">
                                            <option value="default">Default</option>
                                            <option value="dark">Dark</option>
                                            <option value="light">Light</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="theme_color" class="form-label">Theme Color</label>
                                        <select class="form-select" id="theme_color" name="theme_color">
                                            <option value="default">Default</option>
                                            <option value="blue">Blue</option>
                                            <option value="green">Green</option>
                                            <option value="purple">Purple</option>
                                            <option value="red">Red</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sidebar_type" class="form-label">Sidebar Type</label>
                                        <select class="form-select" id="sidebar_type" name="sidebar_type">
                                            <option value="default">Default</option>
                                            <option value="compact">Compact</option>
                                            <option value="icon">Icon</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="direction" class="form-label">Direction</label>
                                        <select class="form-select" id="direction" name="direction">
                                            <option value="ltr">LTR (Left to Right)</option>
                                            <option value="rtl">RTL (Right to Left)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="boxed_layout" name="boxed_layout">
                                        <label class="form-check-label" for="boxed_layout">Boxed Layout</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="card_border" name="card_border">
                                        <label class="form-check-label" for="card_border">Card Border</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Baris 1: Logo dan Dark Logo -->
                            <div class="mb-4">
                                <div class="row">
                                    <!-- Logo Section -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Logo</h5>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-6">
                                                <label for="logo" class="form-label">Upload New</label>
                                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo">
                                                    <label class="form-check-label" for="remove_logo">
                                                        Remove current
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">Current</div>
                                                    <div class="card-body">
                                                        <div id="logo-current-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">New Preview</div>
                                                    <div class="card-body">
                                                        <div id="logo-new-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Dark Logo Section -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Dark Logo</h5>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-6">
                                                <label for="logo_dark" class="form-label">Upload New</label>
                                                <input type="file" class="form-control" id="logo_dark" name="logo_dark" accept="image/*">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" id="remove_logo_dark" name="remove_logo_dark">
                                                    <label class="form-check-label" for="remove_logo_dark">
                                                        Remove current
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">Current</div>
                                                    <div class="card-body">
                                                        <div id="logo-dark-current-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">New Preview</div>
                                                    <div class="card-body">
                                                        <div id="logo-dark-new-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Baris 2: Banner -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mb-3">Banner</h5>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-6">
                                                <label for="banner" class="form-label">Upload New</label>
                                                <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" id="remove_banner" name="remove_banner">
                                                    <label class="form-check-label" for="remove_banner">
                                                        Remove current
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">Current</div>
                                                    <div class="card-body">
                                                        <div id="banner-current-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 150px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">New Preview</div>
                                                    <div class="card-body">
                                                        <div id="banner-new-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 150px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Baris 3: Favicon dan Login Background -->
                            <div class="mb-4">
                                <div class="row">
                                    <!-- Favicon Section -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Favicon</h5>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-6">
                                                <label for="favicon" class="form-label">Upload New</label>
                                                <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" id="remove_favicon" name="remove_favicon">
                                                    <label class="form-check-label" for="remove_favicon">
                                                        Remove current
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">Current</div>
                                                    <div class="card-body">
                                                        <div id="favicon-current-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">New Preview</div>
                                                    <div class="card-body">
                                                        <div id="favicon-new-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Login Background Section -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Login Background</h5>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-6">
                                                <label for="login_background" class="form-label">Upload New</label>
                                                <input type="file" class="form-control" id="login_background" name="login_background" accept="image/*">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" id="remove_login_background" name="remove_login_background">
                                                    <label class="form-check-label" for="remove_login_background">
                                                        Remove current
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">Current</div>
                                                    <div class="card-body">
                                                        <div id="login-background-current-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">New Preview</div>
                                                    <div class="card-body">
                                                        <div id="login-background-new-preview" class="border rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; overflow: hidden;">
                                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- SEO Tab -->
                    <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                        <form id="seo-form">
                            <div class="mb-3">
                                <label for="keyword" class="form-label">Keywords</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" placeholder="keyword1, keyword2, keyword3">
                                <div class="form-text">Separate keywords with commas</div>
                            </div>
                            <div class="mb-3">
                                <label for="keyword_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="keyword_description" name="keyword_description" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="embed_youtube" class="form-label">YouTube Embed Code</label>
                                <textarea class="form-control" id="embed_youtube" name="embed_youtube" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="embed_map" class="form-label">Google Maps Embed Code</label>
                                <textarea class="form-control" id="embed_map" name="embed_map" rows="4"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" id="reset-btn">Reset</button>
                    <button type="button" class="btn btn-primary" id="save-profile-btn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Update Profile</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const profileUpdateUrl = "{{ route('profile.update', ':id') }}";
        const csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/profile.js') }}"></script>
@endpush