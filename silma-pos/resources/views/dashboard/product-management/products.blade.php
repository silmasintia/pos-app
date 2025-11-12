@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Product Management</h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addProductModal">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="products-table" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Image</th>
                                    <th>Product Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Base Unit</th>
                                    <th>Stock</th>
                                    <th>Purchase Price</th>
                                    <th>Cost Price</th>
                                    <th>Price Before Discount</th>
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Code <span class="text-danger">*</span></label>
                                    <input type="text" name="product_code" class="form-control" placeholder="e.g. PRD001"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" name="barcode" class="form-control" placeholder="e.g. 1234567890">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Laptop"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Base Unit <span class="text-danger">*</span></label>
                                    <select name="base_unit_id" class="form-select" required>
                                        <option value="">Select Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Base Stock <span class="text-danger">*</span></label>
                                    <input type="number" name="base_stock" class="form-control" placeholder="e.g. 10"
                                        min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                    <input type="number" name="purchase_price" class="form-control"
                                        placeholder="e.g. 1000000" step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Cost Price <span class="text-danger">*</span></label>
                                    <input type="number" name="cost_price" class="form-control" placeholder="e.g. 1200000"
                                        step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Price Before Discount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="price_before_discount" class="form-control"
                                        placeholder="e.g. 1500000" step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div id="image-container-new"
                                                class="border rounded d-flex align-items-center justify-content-center"
                                                style="width: 100px; height: 100px; overflow: hidden;">
                                                <i class="fas fa-image fa-2x text-secondary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <input type="file" id="image-new" name="image" class="form-control"
                                                accept="image/*" onchange="previewProductImage(this, 'new')">
                                            <small class="text-muted">JPG, PNG, GIF up to 2MB</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Unit Note</label>
                                    <input type="text" name="unit_note" class="form-control"
                                        placeholder="e.g. Base unit">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status_active"
                                            id="status_active_new" checked>
                                        <label class="form-check-label" for="status_active_new">
                                            Active
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status_discount"
                                            id="status_discount_new">
                                        <label class="form-check-label" for="status_discount_new">
                                            Discount
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status_display"
                                            id="status_display_new" checked>
                                        <label class="form-check-label" for="status_display_new">
                                            Display
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Position</label>
                                    <input type="number" name="position" class="form-control" placeholder="e.g. 1"
                                        min="0">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Reminder</label>
                                    <input type="number" name="reminder" class="form-control" placeholder="e.g. 5"
                                        min="0">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Expire Date</label>
                                    <input type="date" name="expire_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link</label>
                            <input type="url" name="link" class="form-control"
                                placeholder="e.g. https://example.com">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addProductForm" class="btn btn-primary btn-submit"
                        data-loading-text="Adding...">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Add Product</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="product_id" id="edit_product_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Code <span class="text-danger">*</span></label>
                                    <input type="text" name="product_code" id="edit_product_code"
                                        class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" name="barcode" id="edit_barcode" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="edit_name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="edit_category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Base Unit <span class="text-danger">*</span></label>
                                    <select name="base_unit_id" id="edit_base_unit_id" class="form-select" required>
                                        <option value="">Select Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Base Stock <span class="text-danger">*</span></label>
                                    <input type="number" name="base_stock" id="edit_base_stock" class="form-control"
                                        min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                    <input type="number" name="purchase_price" id="edit_purchase_price"
                                        class="form-control" step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Cost Price <span class="text-danger">*</span></label>
                                    <input type="number" name="cost_price" id="edit_cost_price" class="form-control"
                                        step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Price Before Discount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="price_before_discount" id="edit_price_before_discount"
                                        class="form-control" step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div id="image-container-edit"
                                                class="border rounded d-flex align-items-center justify-content-center"
                                                style="width: 100px; height: 100px; overflow: hidden;">
                                                <i class="fas fa-image fa-2x text-secondary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" id="image-edit" name="image" class="form-control"
                                                accept="image/*" onchange="previewProductImage(this, 'edit')">
                                            <small class="text-muted">JPG, PNG, GIF up to 2MB</small>

                                            <div class="form-check mt-2">
                                                <input type="hidden" name="remove_image" value="0">
                                                <input class="form-check-input" type="checkbox" id="remove_image_edit"
                                                    name="remove_image" value="1">
                                                <label class="form-check-label" for="remove_image_edit">
                                                    Remove current image
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Unit Note</label>
                                    <input type="text" name="unit_note" id="edit_unit_note" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status_active"
                                            id="edit_status_active">
                                        <label class="form-check-label" for="edit_status_active">
                                            Active
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status_discount"
                                            id="edit_status_discount">
                                        <label class="form-check-label" for="edit_status_discount">
                                            Discount
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status_display"
                                            id="edit_status_display">
                                        <label class="form-check-label" for="edit_status_display">
                                            Display
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Position</label>
                                    <input type="number" name="position" id="edit_position" class="form-control"
                                        min="0">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Reminder</label>
                                    <input type="number" name="reminder" id="edit_reminder" class="form-control"
                                        min="0">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Expire Date</label>
                                    <input type="date" name="expire_date" id="edit_expire_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" id="edit_note" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link</label>
                            <input type="url" name="link" id="edit_link" class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editProductForm" class="btn btn-primary btn-submit"
                        data-loading-text="Updating...">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Update Product</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const productsDataUrl = "{{ route('product-management.products.data') }}";
        const productsStoreUrl = "{{ route('product-management.products.store') }}";
        const productsUpdateUrl = "{{ route('product-management.products.update', ':id') }}";
        const productsDeleteUrl = "{{ route('product-management.products.destroy', ':id') }}";
        const csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/product-management/products.js') }}"></script>
@endpush
