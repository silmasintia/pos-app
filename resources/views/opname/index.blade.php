@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Stock Opname Management</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addStockOpnameModal">
                        <i class="fas fa-plus"></i> Add Stock Opname
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="stock-opnames-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Opname Number</th>
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

<!-- Add Stock Opname Modal -->
<div class="modal fade" id="addStockOpnameModal" tabindex="-1" aria-labelledby="addStockOpnameModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStockOpnameModalLabel">Add New Stock Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStockOpnameForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Opname Date</label>
                                <input type="date" name="opname_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Image / Document</label>
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
                                               accept="image/*"
                                               onchange="previewStockOpnameImage(this, 'new')">
                                        <small class="text-muted">JPG, PNG, GIF up to 2MB</small>
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
                            <h5 class="mb-0">Stock Opname Items</h5>
                            <button type="button" id="addItemBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>System Stock</th>
                                        <th>Physical Stock</th>
                                        <th>Difference</th>
                                        <th>Description</th>
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
                                            <input type="text" class="form-control system-stock-input" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][physical_stock]" class="form-control physical-stock-input" min="0" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control difference-input" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][description_detail]" class="form-control">
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
                <button type="submit" form="addStockOpnameForm" class="btn btn-primary btn-submit" data-loading-text="Saving...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Save Stock Opname</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Stock Opname Modal -->
<div class="modal fade" id="editStockOpnameModal" tabindex="-1" aria-labelledby="editStockOpnameModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockOpnameModalLabel">Edit Stock Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editStockOpnameForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="stock_opname_id" id="edit_stock_opname_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Opname Date</label>
                                <input type="date" name="opname_date" id="edit_opname_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Opname Number</label>
                                <input type="text" id="edit_opname_number" class="form-control" readonly>
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
                                <div id="image-container-edit" class="border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; overflow: hidden;">
                                    <i class="fas fa-image fa-2x text-secondary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" id="image-edit" name="image" class="form-control" accept="image/*" onchange="previewStockOpnameImage(this, 'edit')">
                                <small class="text-muted">JPG, PNG, GIF up to 2MB</small>
                                
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image_edit" name="remove_image" value="1">
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
                            <h5 class="mb-0">Stock Opname Items</h5>
                            <button type="button" id="editAddItemBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="editItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>System Stock</th>
                                        <th>Physical Stock</th>
                                        <th>Difference</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editStockOpnameForm" class="btn btn-primary btn-submit" data-loading-text="Updating...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Update Stock Opname</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Stock Opname Modal -->
<div class="modal fade" id="viewStockOpnameModal" tabindex="-1" aria-labelledby="viewStockOpnameModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewStockOpnameModalLabel">Stock Opname Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Opname Number:</strong> <span id="view_opname_number"></span></p>
                        <p><strong>Date:</strong> <span id="view_opname_date"></span></p>
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
                
                <h5 class="mb-3">Stock Opname Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>System Stock</th>
                                <th>Physical Stock</th>
                                <th>Difference</th>
                                <th>Description</th>
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
        const stockOpnamesDataUrl   = "{{ route('stock-opname.data') }}";
        const stockOpnamesStoreUrl  = "{{ route('stock-opname.store') }}";
        const stockOpnamesUpdateUrl = "{{ route('stock-opname.update', ':id') }}";
        const stockOpnamesDeleteUrl = "{{ route('stock-opname.destroy', ':id') }}";
        const stockOpnamesViewUrl   = "{{ route('stock-opname.show', ':id') }}";
        const stockOpnamesEditUrl   = "{{ route('stock-opname.edit', ':id') }}";
        const productsUrl           = "{{ route('stock-opname.products') }}";
        const csrfToken             = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/opname/index.js') }}"></script>
@endpush