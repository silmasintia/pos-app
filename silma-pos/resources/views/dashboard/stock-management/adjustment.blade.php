@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Stock Adjustment Management</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addAdjustmentModal">
                        <i class="fas fa-plus"></i> Add Adjustment
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="adjustments-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Adjustment Number</th>
                                <th>Date</th>
                                <th>Total Items</th>
                                <th>Description</th>
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

<!-- Add Adjustment Modal -->
<div class="modal fade" id="addAdjustmentModal" tabindex="-1" aria-labelledby="addAdjustmentModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdjustmentModalLabel">Add New Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAdjustmentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Adjustment Date</label>
                                <input type="date" name="adjustment_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Image / Document</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div id="image-container-new" 
                                             class="border rounded d-flex align-items-center justify-content-center"
                                             style="width: 100px; height: 100px; overflow: hidden; cursor: pointer;"
                                             onclick="clearAdjustmentImagePreview('new')"
                                             title="Click to remove image">
                                            <i class="fas fa-image fa-2x text-secondary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="file" id="image-new" name="image" class="form-control"
                                               accept="image/jpeg,image/png,image/jpg,image/gif"
                                               onchange="previewAdjustmentImage(this, 'new')">
                                        <small class="text-muted">JPG, PNG, GIF up to 2MB</small>
                                        <div class="mt-1">
                                            {{-- <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                    onclick="clearAdjustmentImagePreview('new')">
                                                <i class="fas fa-times"></i> Clear
                                            </button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Adjustment Items</h5>
                            <button type="button" id="addItemBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Adjustment Qty</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][product_id]" class="form-control product-select" required>
                                                <option value="">Select Product</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control current-stock-input" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][reason]" class="form-control">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addAdjustmentForm" class="btn btn-primary btn-submit" data-loading-text="Saving...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Save Adjustment</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Adjustment Modal -->
<div class="modal fade" id="editAdjustmentModal" tabindex="-1" aria-labelledby="editAdjustmentModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdjustmentModalLabel">Edit Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAdjustmentForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="adjustment_id" id="edit_adjustment_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Adjustment Date</label>
                                <input type="date" name="adjustment_date" id="edit_adjustment_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Adjustment Number</label>
                                <input type="text" id="edit_adjustment_number" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image / Document</label>
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
                                       accept="image/jpeg,image/png,image/jpg,image/gif" 
                                       onchange="previewAdjustmentImage(this, 'edit')">
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
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Adjustment Items</h5>
                            <button type="button" id="editAddItemBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="editItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Adjustment Qty</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Items will be added dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editAdjustmentForm" class="btn btn-primary btn-submit" data-loading-text="Updating...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Update Adjustment</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Adjustment Modal -->
<div class="modal fade" id="viewAdjustmentModal" tabindex="-1" aria-labelledby="viewAdjustmentModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAdjustmentModalLabel">Stock Adjustment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Adjustment Number:</strong> <span id="view_adjustment_number"></span></p>
                        <p><strong>Date:</strong> <span id="view_adjustment_date"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Items:</strong> <span id="view_total_items"></span></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p><strong>Description:</strong></p>
                    <p id="view_description"></p>
                </div>
                
                <div class="mb-3">
                    <p><strong>Image / Document:</strong></p>
                    <div id="view_image_container" class="border rounded d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; overflow: hidden;">
                        <i class="fas fa-image fa-3x text-secondary"></i>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Adjustment Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Product Code</th>
                                <th>Adjustment Qty</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody id="view_items_container">
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        const adjustmentsDataUrl   = "{{ route('adjustment.data') }}";
        const adjustmentsStoreUrl  = "{{ route('adjustment.store') }}";
        const adjustmentsUpdateUrl = "{{ route('adjustment.update', ':id') }}";
        const adjustmentsDeleteUrl = "{{ route('adjustment.destroy', ':id') }}";
        const adjustmentsViewUrl   = "{{ route('adjustment.show', ':id') }}";
        const adjustmentsEditUrl   = "{{ route('adjustment.edit', ':id') }}";
        const productsUrl           = "{{ route('adjustment.products') }}";
        const csrfToken             = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/stock-management/adjustment.js') }}"></script>
@endpush