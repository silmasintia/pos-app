@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <!-- Profile Header -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card profile-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body p-0">
                    <!-- Cover Image -->
                    <div class="profile-cover position-relative overflow-hidden" style="height: 200px;">
                        <img src="{{ $user->banner ? asset('storage/' . $user->banner) : asset('assets/images/banner/default-banner.jpg') }}" 
                             alt="Profile Banner" 
                             class="img-fluid w-100 h-100"
                             style="object-fit: cover; object-position: center;">
                        <div class="profile-img position-absolute bottom-0 start-0 translate-middle-y ms-4">
                            <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('assets/images/avatars/default.png') }}" 
                                 alt="Profile Picture" 
                                 class="img-fluid rounded-circle border-4 border-white"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                    </div>
                    
                    <!-- Profile Info -->
                    <div class="profile-info p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="mb-1">{{ $user->name }}</h3>
                                <p class="text-muted mb-2">{{ $user->username }}</p>
                                <p class="mb-0">{{ $user->about ?: 'No bio available' }}</p>
                                
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                    <span class="badge bg-info">
                                        {{ $user->status_display ?: 'Standard User' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Details -->
    <div class="row mt-4">
        <!-- Contact Information -->
        <div class="col-lg-4">
            <div class="card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-body">
                    <h5 class="card-title mb-4">Contact Information</h5>
                    
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted">Email</p>
                                <h6>{{ $user->email }}</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-soft-success text-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted">Phone Number</p>
                                <h6>{{ $user->phone_number ?: 'Not provided' }}</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-soft-info text-info rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted">WhatsApp</p>
                                <h6>{{ $user->wa_number ?: 'Not provided' }}</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="d-flex align-items-start">
                            <div class="icon-box bg-soft-warning text-warning rounded-circle me-3 d-flex align-items-center justify-content-center mt-1" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted">Address</p>
                                <h6>{{ $user->address ?: 'Not provided' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Details -->
            <div class="card mt-4" data-aos="fade-up" data-aos-delay="500">
                <div class="card-body">
                    <h5 class="card-title mb-4">Account Details</h5>
                    
                    <div class="account-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">User ID</span>
                            <span>#{{ $user->id }}</span>
                        </div>
                    </div>
                    
                    <div class="account-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Username</span>
                            <span>{{ $user->username }}</span>
                        </div>
                    </div>
                    
                    <div class="account-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Member Since</span>
                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="account-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Last Updated</span>
                            <span>{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- About & Description -->
        <div class="col-lg-8">
            <div class="card" data-aos="fade-up" data-aos-delay="600">
                <div class="card-body">
                    <h5 class="card-title mb-4">About</h5>
                    <div class="about-content">
                        <p>{{ $user->description ?: 'No description available.' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Image Gallery -->
            <div class="card mt-4" data-aos="fade-up" data-aos-delay="700">
                <div class="card-body">
                    <h5 class="card-title mb-4">Images</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="image-preview">
                                <label class="form-label">Profile Picture</label>
                                <div class="preview-container border rounded p-3 text-center">
                                    <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('assets/images/avatars/default.png') }}" 
                                         alt="Profile Picture" 
                                         class="img-fluid rounded-circle"
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="image-preview">
                                <label class="form-label">Banner Image</label>
                                <div class="preview-container border rounded p-3 text-center">
                                    <img src="{{ $user->banner ? asset('storage/' . $user->banner) : asset('assets/images/banner/default-banner.jpg') }}" 
                                         alt="Banner Image" 
                                         class="img-fluid rounded"
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status & Activity -->
            <div class="card mt-4" data-aos="fade-up" data-aos-delay="800">
                <div class="card-body">
                    <h5 class="card-title mb-4">Status & Activity</h5>
                    <div class="status-container">
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator me-3">
                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }} p-2 rounded-circle">
                                    <i class="fas fa-circle"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">Account Status</h6>
                                <p class="mb-0 text-muted">{{ ucfirst($user->status) }}</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="status-indicator me-3">
                                <span class="badge bg-info p-2 rounded-circle">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">Display Status</h6>
                                <p class="mb-0 text-muted">{{ $user->status_display ?: 'Standard User' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
             <div class="modal-body" style="overflow-y: auto !important; max-height: 70vh !important;">
                    <!-- Personal Information -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Personal Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Additional Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="wa_number" class="form-label">WhatsApp Number</label>
                                <input type="text" class="form-control @error('wa_number') is-invalid @enderror" id="wa_number" name="wa_number" value="{{ old('wa_number', $user->wa_number) }}">
                                @error('wa_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="1">{{ old('address', $user->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="about" class="form-label">Bio</label>
                            <textarea class="form-control @error('about') is-invalid @enderror" id="about" name="about" rows="2">{{ old('about', $user->about) }}</textarea>
                            @error('about') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $user->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Profile Images -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Profile Images</h6>
                        <div class="row">
                            <!-- Profile Picture -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Profile Picture</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text mb-2">Leave empty to keep current image</div>
                                <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('assets/images/avatars/default.png') }}" 
                                     class="img-thumbnail rounded-circle" style="width:80px;height:80px;object-fit:cover;" id="currentProfileImage">
                                <div class="d-none mt-2" id="newProfileImagePreview">
                                    <img src="" class="img-thumbnail rounded-circle" style="width:80px;height:80px;object-fit:cover;" id="previewProfileImage">
                                </div>
                            </div>
                            <!-- Banner -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Banner Image</label>
                                <input type="file" class="form-control @error('banner') is-invalid @enderror" id="banner" name="banner" accept="image/*">
                                @error('banner') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text mb-2">Leave empty to keep current banner</div>
                                <img src="{{ $user->banner ? asset('storage/' . $user->banner) : asset('assets/images/banner/default-banner.jpg') }}" 
                                     class="img-thumbnail rounded" style="width:150px;height:80px;object-fit:cover;" id="currentBannerImage">
                                <div class="d-none mt-2" id="newBannerImagePreview">
                                    <img src="" class="img-thumbnail rounded" style="width:150px;height:80px;object-fit:cover;" id="previewBannerImage">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Change Password</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Leave empty to keep current password</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
                                @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile image preview
    const profileImageInput = document.getElementById('image');
    const profileImagePreview = document.getElementById('previewProfileImage');
    const newProfileImagePreview = document.getElementById('newProfileImagePreview');

    profileImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImagePreview.src = e.target.result;
                newProfileImagePreview.classList.remove('d-none');
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            newProfileImagePreview.classList.add('d-none');
        }
    });

    // Banner preview
    const bannerInput = document.getElementById('banner');
    const bannerPreview = document.getElementById('previewBannerImage');
    const newBannerPreview = document.getElementById('newBannerImagePreview');

    bannerInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                bannerPreview.src = e.target.result;
                newBannerPreview.classList.remove('d-none');
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            newBannerPreview.classList.add('d-none');
        }
    });
});
</script>
@endsection