@extends('layouts.app')

@section('title', 'Log Histories Report')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Log Histories Report</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                    <div class="col-md-3">
                        <label for="user_id" class="form-label">User</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="action" class="form-label">Action</label>
                        <select class="form-select" id="action" name="action">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="table_name" class="form-label">Table</label>
                        <select class="form-select" id="table_name" name="table_name">
                            <option value="">All Tables</option>
                            @foreach($tables as $table)
                                <option value="{{ $table }}">{{ ucfirst(str_replace('_', ' ', $table)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button class="btn btn-primary" id="reset-filter">
                                <i class="fas fa-sync-alt me-2"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="log-histories-table" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Table</th>
                                <th>Action</th>
                                <th>Entity ID</th>
                                <th>Changes</th>
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

<div class="modal fade" id="viewLogModal" tabindex="-1" aria-labelledby="viewLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLogModalLabel">Log History Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Log Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Table:</td>
                                <td id="log-table-name"></td>
                            </tr>
                            <tr>
                                <td>Entity ID:</td>
                                <td id="log-entity-id"></td>
                            </tr>
                            <tr>
                                <td>Action:</td>
                                <td id="log-action"></td>
                            </tr>
                            <tr>
                                <td>Date & Time:</td>
                                <td id="log-timestamp"></td>
                            </tr>
                            <tr>
                                <td>User:</td>
                                <td id="log-user-name"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Summary</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p id="log-summary" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h6>Changes Details</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody id="log-changes">
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
<script src="{{ asset('js/reports/log-histories.js') }}"></script>
@endpush