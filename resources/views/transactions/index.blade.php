@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Transactions Management</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addTransactionModal">
                        <i class="fas fa-plus"></i> Add Transaction
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="transactions-table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Cash Account</th>
                                <th>Amount</th>
                                <th>Image</th>
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

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTransactionModalLabel">Add New Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTransactionForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Transaction Type</label>
                                <select name="transaction_type" id="transaction_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="income">Income (Cash In)</option>
                                    <option value="expense">Expense (Cash Out)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="transaction_category_id" id="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cash Account</label>
                                <select name="cash_id" class="form-select" required>
                                    <option value="">Select Cash Account</option>
                                    @foreach($cashAccounts as $cash)
                                        <option value="{{ $cash->id }}">{{ $cash->name }} (Rp {{ number_format($cash->amount, 2, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Transaction Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Office Supplies" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="0.00" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div id="image-container-new" class="border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; overflow: hidden;">
                                    <i class="fas fa-image fa-2x text-secondary"></i>
                                </div>
                            </div>
                            <div>
                                <input type="file" id="image-new" name="image" class="form-control" accept="image/*" onchange="previewTransactionImage(this, 'new')">
                                <small class="text-muted">JPG, PNG, GIF up to 2MB</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addTransactionForm" class="btn btn-primary btn-submit" data-loading-text="Adding...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Add Transaction</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTransactionModalLabel">Edit Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="transaction_id" id="edit_transaction_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" id="edit_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Transaction Type</label>
                                <select name="transaction_type" id="edit_transaction_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="income">Income (Cash In)</option>
                                    <option value="expense">Expense (Cash Out)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="transaction_category_id" id="edit_category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cash Account</label>
                                <select name="cash_id" id="edit_cash_id" class="form-select" required>
                                    <option value="">Select Cash Account</option>
                                    @foreach($cashAccounts as $cash)
                                        <option value="{{ $cash->id }}">{{ $cash->name }} (Rp {{ number_format($cash->amount, 2, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Transaction Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image</label>
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editTransactionForm" class="btn btn-primary btn-submit" data-loading-text="Updating...">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Update Transaction</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        const transactionsDataUrl   = "{{ route('transactions.data') }}";
        const transactionsStoreUrl  = "{{ route('transactions.store') }}";
        const transactionsUpdateUrl = "{{ route('transactions.update', ':id') }}";
        const transactionsDeleteUrl = "{{ route('transactions.destroy', ':id') }}";
        const csrfToken      = "{{ csrf_token() }}";
        const assetUrl       = "{{ asset('') }}";
    </script>
    <script src="{{ asset('js/transactions/index.js') }}"></script>
@endpush