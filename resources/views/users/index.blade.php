@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title mb-0">User Management</h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">
                            <i class="fas fa-plus"></i> Add User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" enctype="multipart/form-data">
                        @csrf
                        <!-- Banner & Profile Image -->
                        <div class="position-relative mb-4" style="height: 200px; overflow: hidden; border-radius: 8px;">
                            <div id="banner-container-new"
                                class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
                                <span class="text-white">Banner</span>
                            </div>
                            <label for="banner-new"
                                class="position-absolute bottom-0 end-0 m-2 bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                                style="cursor: pointer; z-index: 10; width: 36px; height: 36px;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="banner-new" name="banner" class="d-none" accept="image/*"
                                onchange="previewBanner(this, 'new')">

                            <div class="position-absolute" style="bottom: 20px; left: 20px;">
                                <div class="position-relative">
                                    <div id="profile-image-container-new"
                                        class="rounded-circle border-4 border-white bg-light d-flex align-items-center justify-content-center shadow"
                                        style="width: 100px; height: 100px;">
                                        <i class="fas fa-user fa-2x text-secondary"></i>
                                    </div>
                                    <label for="new-image"
                                        class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 d-flex align-items-center justify-content-center"
                                        style="cursor: pointer; z-index: 10; width: 28px; height: 28px;">
                                        <i class="fas fa-camera fs-6"></i>
                                    </label>
                                    <input type="file" id="new-image" name="image" class="d-none" accept="image/*"
                                        onchange="previewProfileImage(this, 'new')">
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. John Doe"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="e.g. johndoe"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    placeholder="e.g. johndoe@example.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control"
                                    placeholder="e.g. +1234567890">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp Number</label>
                                <input type="text" name="wa_number" class="form-control"
                                    placeholder="e.g. +1234567890">
                            </div>
                        </div>

                        <!-- Role Selection & Address -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">About</label>
                            <textarea name="about" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>

                        <!-- Status Switches -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="hidden" name="status" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Display</label>
                                <input type="hidden" name="status_display" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status_display" value="1"
                                        checked>
                                    <label class="form-check-label">Public</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addUserForm" class="btn btn-primary btn-submit"
                        data-loading-text="Adding...">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Add User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" id="edit_user_id">

                        <!-- Banner & Profile Image -->
                        <div class="position-relative mb-4" style="height: 200px; overflow: hidden; border-radius: 8px;">
                            <div id="banner-container-edit"
                                class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
                                <span class="text-white">Banner</span>
                            </div>
                            <label for="banner-edit"
                                class="position-absolute bottom-0 end-0 m-2 bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                                style="cursor: pointer; z-index: 10; width: 36px; height: 36px;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="banner-edit" name="banner" class="d-none" accept="image/*"
                                onchange="previewBanner(this, 'edit')">

                            <div class="position-absolute" style="bottom: 20px; left: 20px;">
                                <div class="position-relative">
                                    <div id="profile-image-container-edit"
                                        class="rounded-circle border-4 border-white bg-light d-flex align-items-center justify-content-center shadow"
                                        style="width: 100px; height: 100px;">
                                        <i class="fas fa-user fa-2x text-secondary"></i>
                                    </div>
                                    <label for="image-edit"
                                        class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 d-flex align-items-center justify-content-center"
                                        style="cursor: pointer; z-index: 10; width: 28px; height: 28px;">
                                        <i class="fas fa-camera fs-6"></i>
                                    </label>
                                    <input type="file" id="image-edit" name="image" class="d-none"
                                        accept="image/*" onchange="previewProfileImage(this, 'edit')">
                                </div>
                            </div>
                        </div>

                        <!-- Remove Images -->
                        <div class="ps-5 mt-5 mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="remove-image-edit"
                                            name="remove_image" value="1">
                                        <label class="form-check-label" for="remove-image-edit">
                                            Remove current profile image
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="remove-banner-edit"
                                            name="remove_banner" value="1">
                                        <label class="form-check-label" for="remove-banner-edit">
                                            Remove current banner
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" id="edit_username" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" id="edit_password" class="form-control"
                                    placeholder="Leave blank to keep current">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" id="edit_phone_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp Number</label>
                                <input type="text" name="wa_number" id="edit_wa_number" class="form-control">
                            </div>
                        </div>

                        <!-- Role Selection & Address -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" id="edit_role" class="form-select">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">About</label>
                            <textarea name="about" id="edit_about" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="4"></textarea>
                        </div>

                        <!-- Status Switches -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <input type="hidden" name="status" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="edit_status"
                                        value="1">
                                    <label class="form-check-label" id="edit_status_label">Inactive</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Display</label>
                                <input type="hidden" name="status_display" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status_display"
                                        id="edit_status_display" value="1">
                                    <label class="form-check-label" id="edit_status_display_label">Private</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editUserForm" class="btn btn-primary btn-submit"
                        data-loading-text="Updating...">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Update User</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const usersDataUrl = "{{ route('users.data') }}";
        const usersStoreUrl = "{{ route('users.store') }}";
        const csrfToken = "{{ csrf_token() }}";
        const currentUserId = {{ Auth::id() }};
    </script>
    <script src="{{ asset('js/users/index.js') }}"></script>
@endpush