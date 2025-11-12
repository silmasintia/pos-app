@extends('layouts.app')

@section('styles')
<style>
    .pos-container {
        background-color: var(--bs-body-tertiary);
        min-height: calc(100vh - 120px);
    }

    .product-card {
        border: 1px solid var(--bs-border-color-translucent);
        border-radius: 0.375rem;
        transition: all 0.2s ease-in-out;
        background-color: var(--bs-card-bg);
        height: 100%;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--bs-box-shadow-sm);
    }

    .product-image-wrapper {
        aspect-ratio: 4 / 3;
        overflow: hidden;
        border-radius: 0.375rem 0.375rem 0 0;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-table th {
        background-color: #4a6fdc;
        color: white;
        font-weight: 500;
        border-color: #4a6fdc;
    }
    
    .cart-item-quantity {
        max-width: 70px;
    }
    
    #empty-cart-container {
        min-height: 200px;
    }

    .order-summary {
        background-color: var(--bs-card-bg);
        border-radius: 0.75rem;
        border: 1px solid var(--bs-border-color-translucent);
    }

    .order-summary .card-header {
        background-color: #4a6fdc;
        color: white;
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }
    
    .order-summary-row {
        padding: 0.6rem 0;
    }

    .order-summary-row:not(:last-child) {
        border-bottom: 1px dashed var(--bs-border-color-translucent);
    }

    .total-row {
        font-weight: 600;
        font-size: 1.2rem;
        padding: 1rem 0;
        border-top: 1px solid var(--bs-border-color);
        margin-top: 0.5rem;
    }

    .product-grid {
        max-height: calc(100vh - 450px);
        overflow-y: auto;
        padding-right: 10px;
    }

    .product-grid::-webkit-scrollbar {
        width: 8px;
    }

    .product-grid::-webkit-scrollbar-thumb {
        background-color: #ced4da;
        border-radius: 4px;
    }

    .card-body.p-2 {
        padding: 0.5rem !important;
    }

    .card-title.small {
        font-size: 0.75rem !important;
    }

    .card-text.small {
        font-size: 0.7rem !important;
    }
</style>
@endsection

@section('content')
<div class="pos-container p-4">
    <div class="row g-4">
        {{-- Main Content: Product Selection & Cart --}}
        <div class="col-lg-8">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 fw-bold">Point of Sale</h4>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="clear-cart-btn">
                        <i class="fas fa-trash-alt me-1"></i> Clear Cart
                    </button>
                </div>
                <div class="card-body d-flex flex-column">
                    {{-- Customer & Cash Account Selection --}}
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label for="customer-select" class="form-label fw-medium">Customer</label>
                            <select class="form-select" id="customer-select">
                                <option value="10" selected>Umum</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="cash-select" class="form-label fw-medium">Cash Account</label>
                            <select class="form-select" id="cash-select">
                                @foreach($cashAccounts as $cash)
                                <option value="{{ $cash->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $cash->name }} (Rp {{ number_format($cash->amount, 0, ',', '.') }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Product Search & Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-8 mb-2 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search products by name, code, or barcode..." id="product-search">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="category-filter">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\Categories::orderBy('name')->get() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Product Grid --}}
                    <div class="row g-3 product-grid" id="product-container">
                        {{-- Products will be loaded here by pos.js --}}
                    </div>
                    
                    {{-- Cart Table --}}
                    <div class="mt-auto pt-3">
                        <h5 class="fw-bold mb-2">Cart</h5>
                        <div class="table-responsive">
                            <table class="table table-sm cart-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="45%">Product</th>
                                        <th width="20%">Price</th>
                                        <th width="15%">Qty</th>
                                        <th width="20%">Total</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <tr id="empty-cart-row">
                                        <td colspan="5">
                                            <div id="empty-cart-container" class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                <p class="mb-0 fs-5">Cart is empty</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Order Summary --}}
        <div class="col-lg-4">
            <div class="order-summary card border-0 shadow-sm">
                <div class="card-header py-3">
                    <h4 class="card-title mb-0 fw-bold">Order Summary</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="order-summary-row d-flex justify-content-between align-items-center">
                            <span>Subtotal</span>
                            <span id="subtotal" class="fw-medium">Rp 0</span>
                        </div>
                        <div class="order-summary-row">
                             <div class="input-group input-group-sm">
                                <span class="input-group-text">Discount %</span>
                                <input type="number" class="form-control" id="percent-discount" min="0" max="100" value="0">
                            </div>
                        </div>
                        <div class="order-summary-row">
                             <div class="input-group input-group-sm">
                                <span class="input-group-text">Discount Rp</span>
                                <input type="number" class="form-control" id="amount-discount" min="0" value="0">
                            </div>
                        </div>
                        <div class="total-row d-flex justify-content-between align-items-center">
                            <span>Total</span>
                            <span id="total-amount" class="text-primary">Rp 0</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment-amount" class="form-label fw-medium">Payment</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control form-control-lg" id="payment-amount" min="0" placeholder="">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span>Change:</span>
                            <span id="change-amount" class="fw-bold fs-5">Rp 0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment-type" class="form-label fw-medium">Payment Type</label>
                        <select class="form-select" id="payment-type">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Transfer</option>
                            <option value="e-wallet">E-Wallet</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="order-notes" class="form-label fw-medium">Notes</label>
                        <textarea class="form-control" id="order-notes" rows="2" placeholder="Optional order notes..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="button" class="btn btn-primary btn-lg py-2" id="checkout-btn">
                            <i class="fas fa-check-circle me-2"></i> Process Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Product Detail Modal --}}
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div id="product-images" class="mb-3"></div>
                    </div>
                    <div class="col-md-7">
                        <h4 id="product-name"></h4>
                        <p id="product-code" class="text-muted"></p>
                        <p id="product-description"></p>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Unit</label>
                            <select class="form-select" id="product-unit"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Quantity</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="decrease-qty"><i class="fas fa-minus"></i></button>
                                <input type="number" class="form-control text-center" id="product-quantity" value="1" min="1">
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="increase-qty"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="product-price" min="0" step="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <span>Stock: </span>
                                <span id="product-stock" class="fw-bold"></span>
                            </div>
                            <div>
                                <span>Subtotal: </span>
                                <span id="product-subtotal" class="fw-bold">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="add-to-cart-btn">
                    <i class="fas fa-cart-plus me-2"></i> Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Order Success Modal --}}
<div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderSuccessModalLabel">Order Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4>Order Completed Successfully!</h4>
                <p class="mb-2">Order Number: <strong id="order-number"></strong></p>
                <p>Thank you for your purchase.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="print-receipt-btn">
                    <i class="fas fa-print me-2"></i> Print Receipt
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productsUrl = "{{ route('sales.products') }}";
    const productUrl = "{{ route('sales.product', ':id') }}";
    const storeOrderUrl = "{{ route('sales.store') }}";
    const printReceiptUrl = "{{ route('sales.print', ':id') }}";
    const csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/sales/pos.js') }}"></script>
@endpush