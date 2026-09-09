@extends('layout.layout')

@php
    $title = __('entities.modules.crm');
    $subTitle = __('entities.sidebar.accounts');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.accounts') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Commercial Entity Accounts, Corporate Wholesale Clients & Embassy Allocations</p>
                </div>
                <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:user-id-outline" class="text-xl"></iconify-icon>
                    <span>Add Corporate Account</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Account Code</th>
                                <th>Entity Name</th>
                                <th>Account Type</th>
                                <th>Key Liaison</th>
                                <th>Credit Limit</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-primary-600">#ACC-901</td>
                                <td>Emirates Diplomatic Academy</td>
                                <td>Institutional Gift Contract</td>
                                <td>Dr. Rashid Al-Nuaimi</td>
                                <td class="fw-bold">AED 500,000</td>
                                <td><span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs">Active</span></td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-primary radius-6">Manage</button></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-primary-600">#ACC-902</td>
                                <td>Burj Al Arab Sovereign Suite</td>
                                <td>Luxury Hospitality Wholesale</td>
                                <td>Elena Rostova (Concierge Director)</td>
                                <td class="fw-bold">AED 350,000</td>
                                <td><span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs">Active</span></td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-primary radius-6">Manage</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
