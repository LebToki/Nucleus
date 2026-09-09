@extends('layout.layout')

@php
    $title = 'Financial Telemetry';
    $subTitle = 'Cash-flow & PnL Overview';
@endphp

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                <span class="text-xs fw-semibold text-warning-700 text-uppercase">Total Clients</span>
                <h3 class="fw-bold text-neutral-900 mt-2 mb-1">15</h3>
                <span class="text-xs text-neutral-500">Active billing accounts</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                <span class="text-xs fw-semibold text-teal-700 text-uppercase">Total Vendors</span>
                <h3 class="fw-bold text-neutral-900 mt-2 mb-1">15</h3>
                <span class="text-xs text-neutral-500">Active payable sources</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                <span class="text-xs fw-semibold text-success-700 text-uppercase">Customer Receipts</span>
                <h3 class="fw-bold text-success-600 mt-2 mb-1">$445,508.26</h3>
                <span class="text-xs text-neutral-500">Received payments</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                <span class="text-xs fw-semibold text-danger-700 text-uppercase">Disbursements</span>
                <h3 class="fw-bold text-danger-600 mt-2 mb-1">$17,872.00</h3>
                <span class="text-xs text-neutral-500">Paid to vendors</span>
            </div>
        </div>
    </div>
</div>
@endsection
