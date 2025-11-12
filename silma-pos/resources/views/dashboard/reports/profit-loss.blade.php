@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('content')
<div class="content-inner py-0">
    <div class="row">
        <div class="col-md-12">
            <div class="card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Profit & Loss Report</h4>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="btn btn-primary btn-sm" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-2"></i>Filter
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <div class="px-4 py-3">
                                    <div class="mb-3">
                                        <label for="start-date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start-date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="end-date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end-date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-primary" id="apply-filter">Apply Filter</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6 col-xl-3">
                            <div class="card border-start border-primary border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="text-gray mb-1">Total Sales</p>
                                            <h4 class="mb-0" id="total-sales">Rp 0</h4>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="avatar-md bg-soft-primary rounded-circle">
                                                <i class="fas fa-shopping-bag avatar-icon font-22"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card border-start border-danger border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="text-gray mb-1">Total Purchases</p>
                                            <h4 class="mb-0" id="total-purchases">Rp 0</h4>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="avatar-md bg-soft-danger rounded-circle">
                                                <i class="fas fa-file-invoice-dollar avatar-icon font-22"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card border-start border-info border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="text-gray mb-1">Gross Profit</p>
                                            <h4 class="mb-0" id="gross-profit">Rp 0</h4>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="avatar-md bg-soft-info rounded-circle">
                                                <i class="fas fa-chart-line avatar-icon font-22"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="text-gray mb-1">Net Profit</p>
                                            <h4 class="mb-0" id="net-profit">Rp 0</h4>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="avatar-md bg-soft-success rounded-circle">
                                                <i class="fas fa-money-bill-wave avatar-icon font-22"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profit & Loss Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="profit-loss-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Source</th>
                                    <th>Reference</th>
                                    <th>Cash</th>
                                    <th class="text-end">Amount</th>
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
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('js/reports/profit-loss.js') }}"></script>
@endpush