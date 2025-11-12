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
            font-size: 0.75rem;
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

        .cart-section {
            margin-top: 1.5rem;
        }

        .load-more-btn {
            margin-top: 1rem;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .cart-icon-container {
            position: relative;
            display: inline-block;
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

        .product-card .card-title {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }

        .product-card .text-muted {
            font-size: 0.7rem;
        }

        .product-card .text-primary {
            font-size: 0.85rem;
        }

        .product-card .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .product-card .form-control {
            padding: 0.25rem;
            font-size: 0.75rem;
            height: 30px;
            color: var(--bs-body-color) !important;
        }

        .product-card .quantity-control {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.8rem !important;
        }

        .product-image-container {
            height: 120px !important;
        }

        .product-image-container img {
            height: 120px !important;
        }

        .product-card .card-body {
            padding: 0.5rem;
        }

        .product-card {
            padding: 0.5rem !important;
        }

        .order-summary {
            min-height: 500px;
        }

        .cart-items-container {
            min-height: 300px;
        }

        .modal-lg {
            max-width: 700px;
        }

        .product-card .text-primary {
            margin-bottom: 0.5rem !important;
        }

        .product-quantity::-webkit-outer-spin-button,
        .product-quantity::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .product-quantity {
            -moz-appearance: textfield;
        }
    </style>
@endsection

@section('content')
    <div class="pos-container p-3">
        <div class="row g-3 mb-3">
            {{-- Point of Sale --}}
            <div class="col-lg-7">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-body-tertiary py-2">
                        <h5 class="card-title mb-0 fw-bold">Point of Sale</h5>
                    </div>
                    <div class="card-body">
                        {{-- Product Search & Filter --}}
                        <div class="row mb-2">
                            <div class="col-md-8 mb-2 mb-md-0">
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control form-control-sm"
                                        placeholder="Search products..." id="product-search">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" id="category-filter">
                                    <option value="">All Categories</option>
                                    @foreach (\App\Models\Categories::orderBy('name')->get() as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Product Grid --}}
                        <div class="row g-2 product-grid" id="product-container">

                        </div>

                        <div class="text-center mt-3">
                            <button class="btn btn-sm btn-outline-primary load-more-btn" id="load-more-btn">
                                <i class="fas fa-plus-circle me-1"></i> Load More
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cart --}}
            <div class="col-lg-5">
                <div class="order-summary card border-0 shadow-sm">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Cart</h5>
                        <div class="d-flex align-items-center">
                            <div class="cart-icon-container me-2">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-badge" id="cart-count">0</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="clear-cart-btn">
                                <i class="fas fa-trash-alt"></i> Clear
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="cart-items-container mb-3" id="cart-items-container">
                            <div class="text-center text-muted py-3">Cart is empty</div>
                        </div>

                        <div class="order-summary-details">
                            <div class="order-summary-row d-flex justify-content-between align-items-center">
                                <span>Subtotal</span>
                                <span id="subtotal" class="fw-medium">Rp 0</span>
                            </div>
                            <div class="total-row d-flex justify-content-between align-items-center">
                                <span>Total</span>
                                <span id="total-amount" class="text-primary">Rp 0</span>
                            </div>
                        </div>

                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-primary py-2" id="checkout-btn">
                                <i class="fas fa-check-circle me-1"></i> Process Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Process Modal --}}
    <div class="modal fade" id="orderProcessModal" tabindex="-1" aria-labelledby="orderProcessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderProcessModalLabel">Process Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label for="modal-customer-select" class="form-label fw-medium">Customer</label>
                            <select class="form-select form-select-sm" id="modal-customer-select">
                                <option value="10" selected>Umum</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="modal-cash-select" class="form-label fw-medium">Cash Account</label>
                            <select class="form-select form-select-sm" id="modal-cash-select">
                                @foreach ($cashAccounts as $cash)
                                    <option value="{{ $cash->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $cash->name }} (Rp {{ number_format($cash->amount, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Discount %</span>
                                <input type="number" class="form-control form-control-sm" id="modal-percent-discount"
                                    min="0" max="100" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Discount Rp</span>
                                <input type="number" class="form-control form-control-sm" id="modal-amount-discount"
                                    min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal-payment-amount" class="form-label fw-medium">Payment</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="modal-payment-amount" min="0"
                                placeholder="">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span>Change:</span>
                            <span id="modal-change-amount" class="fw-bold">Rp 0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal-payment-type" class="form-label fw-medium">Payment Type</label>
                        <select class="form-select form-select-sm" id="modal-payment-type">
                            <option value="cash" selected>Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Transfer</option>
                            <option value="e-wallet">E-Wallet</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modal-order-notes" class="form-label fw-medium">Notes</label>
                        <textarea class="form-control form-control-sm" id="modal-order-notes" rows="2"
                            placeholder="Optional order notes..."></textarea>
                    </div>

                    <div class="order-summary-details mt-3">
                        <div class="order-summary-row d-flex justify-content-between align-items-center">
                            <span>Subtotal</span>
                            <span id="modal-subtotal" class="fw-medium">Rp 0</span>
                        </div>
                        <div class="total-row d-flex justify-content-between align-items-center">
                            <span>Total</span>
                            <span id="modal-total-amount" class="text-primary">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="modal-checkout-btn">
                        <i class="fas fa-check-circle me-1"></i> Confirm Order
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Order Success Modal --}}
    <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessModalLabel"
        aria-hidden="true">
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
                <div class="modal-footer d-flex flex-wrap justify-content-between gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-primary" id="print-receipt-btn">
                        <i class="fas fa-print me-1"></i> Print Receipt
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.posConfig = {
            productsUrl: "{{ route('sales.products') }}",
            productUrl: "{{ route('sales.product', ':id') }}",
            storeOrderUrl: "{{ route('sales.store') }}",
            printReceiptUrl: "{{ route('sales.print', ':id') }}",
            categoriesUrl: "{{ route('sales.categories') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sales/pos.js') }}"></script>
@endpush
