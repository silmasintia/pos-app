@extends('layouts.app')

@section('title', 'Social Media Management')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Social Media Management</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSocialMediaModal">
                        <i class="fas fa-plus"></i> Add Social Media
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="social-media-table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Link</th>
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

<!-- Add Social Media Modal -->
<div class="modal fade" id="addSocialMediaModal" tabindex="-1" aria-labelledby="addSocialMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSocialMediaModalLabel">Add New Social Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-social-media-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="social_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="social_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="social_description" class="form-label">Description</label>
                        <textarea class="form-control" id="social_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="social_link" class="form-label">Link</label>
                        <input type="text" class="form-control" id="social_link" name="link">
                    </div>
                    <div class="mb-3">
                        <label for="social_image" class="form-label">Image</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div id="image-container-add" class="border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; overflow: hidden;">
                                    <i class="fas fa-image fa-2x text-secondary"></i>
                                </div>
                            </div>
                            <div>
                                <input type="file" id="social_image" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'image-container-add')">
                                <small class="text-muted">JPG, PNG, GIF up to 2MB</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="add-social-media-form" class="btn btn-primary btn-submit" data-loading-text="Adding...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Add Social Media</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Social Media Modal -->
<div class="modal fade" id="editSocialMediaModal" tabindex="-1" aria-labelledby="editSocialMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSocialMediaModalLabel">Edit Social Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-social-media-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_social_id">
                    <div class="mb-3">
                        <label for="edit_social_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_social_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_social_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_social_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_social_link" class="form-label">Link</label>
                        <input type="text" class="form-control" id="edit_social_link" name="link">
                    </div>
                    <div class="mb-3">
                        <label for="edit_social_image" class="form-label">Image</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div id="image-container-edit" class="border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; overflow: hidden;">
                                    <i class="fas fa-image fa-2x text-secondary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" id="edit_social_image" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'image-container-edit')">
                                <small class="text-muted">JPG, PNG, GIF up to 2MB</small>
                                
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image_edit" name="remove_image">
                                    <label class="form-check-label" for="remove_image_edit">
                                        Remove current image
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="edit-social-media-form" class="btn btn-primary btn-submit" data-loading-text="Updating...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Update Social Media</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const socialMediaDataUrl = "{{ route('social-media.data') }}";
        const socialMediaStoreUrl = "{{ route('social-media.store') }}";
        const socialMediaUpdateUrl = "{{ route('social-media.update', ':id') }}";
        const socialMediaEditUrl = "{{ route('social-media.edit', ':id') }}";
        const socialMediaDeleteUrl = "{{ route('social-media.delete', ':id') }}";
        const csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/compro/social-media.js') }}"></script>
@endpush