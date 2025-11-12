@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Purchase Management</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addPurchaseModal">
                        <i class="fas fa-plus"></i> Add Purchase
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchases-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Purchase Number</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Cash Account</th>
                                <th>Total</th>
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

<!-- Add Purchase Modal -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-labelledby="addPurchaseModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPurchaseModalLabel">Add New Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPurchaseForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-control select2" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cash Account</label>
                                <select name="cash_id" class="form-control select2" required>
                                    <option value="">Select Cash Account</option>
                                    @foreach($cashAccounts as $cash)
                                        <option value="{{ $cash->id }}">{{ $cash->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Payment Type</label>
                                <select name="type_payment" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Image / Receipt</label>
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
                                               onchange="previewTransactionImage(this, 'new')">
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
                            <h5 class="mb-0">Purchase Items</h5>
                            <button type="button" id="addItemBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
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
                                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][purchase_price]" class="form-control price-input" min="0" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control total-input" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                        <td><input type="text" id="grandTotal" class="form-control" readonly></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addPurchaseForm" class="btn btn-primary btn-submit" data-loading-text="Saving...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Save Purchase</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Purchase Modal -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1" aria-labelledby="editPurchaseModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPurchaseModalLabel">Edit Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPurchaseForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="purchase_id" id="edit_purchase_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" id="edit_purchase_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Purchase Number</label>
                                <input type="text" id="edit_purchase_number" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" id="edit_supplier_id" class="form-control select2" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cash Account</label>
                                <select name="cash_id" id="edit_cash_id" class="form-control select2" required>
                                    <option value="">Select Cash Account</option>
                                    @foreach($cashAccounts as $cash)
                                        <option value="{{ $cash->id }}">{{ $cash->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Payment Type</label>
                                <select name="type_payment" id="edit_type_payment" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image / Receipt</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div id="image-container-edit" class="border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; overflow: hidden;">
                                    <i class="fas fa-image fa-2x text-secondary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" id="image-edit" name="image" class="form-control" accept="image/*" onchange="previewTransactionImage(this, 'edit')">
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
                            <h5 class="mb-0">Purchase Items</h5>
                            <button type="button" id="editAddItemBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="editItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                        <td><input type="text" id="editGrandTotal" class="form-control" readonly></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editPurchaseForm" class="btn btn-primary btn-submit" data-loading-text="Updating...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Update Purchase</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Purchase Modal -->
<div class="modal fade" id="viewPurchaseModal" tabindex="-1" aria-labelledby="viewPurchaseModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPurchaseModalLabel">Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Purchase Number:</strong> <span id="view_purchase_number"></span></p>
                        <p><strong>Date:</strong> <span id="view_purchase_date"></span></p>
                        <p><strong>Supplier:</strong> <span id="view_supplier_name"></span></p>
                        <p><strong>Cash Account:</strong> <span id="view_cash_name"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Payment Type:</strong> <span id="view_type_payment"></span></p>
                        <p><strong>Status:</strong> <span id="view_status"></span></p>
                        <p><strong>Total:</strong> <span id="view_total_cost"></span></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p><strong>Description:</strong></p>
                    <p id="view_description"></p>
                </div>
                
                <div class="mb-3">
                    <p><strong>Image / Receipt:</strong></p>
                    <div id="view_image_container" class="border rounded d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; overflow: hidden;">
                        <i class="fas fa-image fa-3x text-secondary"></i>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Purchase Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
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
        const purchasesDataUrl   = "{{ route('purchases.data') }}";
        const purchasesStoreUrl  = "{{ route('purchases.store') }}";
        const purchasesUpdateUrl = "{{ route('purchases.update', ':id') }}";
        const purchasesDeleteUrl = "{{ route('purchases.destroy', ':id') }}";
        const purchasesViewUrl   = "{{ route('purchases.show', ':id') }}";
        const purchasesEditUrl   = "{{ route('purchases.edit', ':id') }}";
        const productsUrl        = "{{ route('purchases.products') }}";
        const csrfToken          = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/purchases.js') }}"></script>
@endpush