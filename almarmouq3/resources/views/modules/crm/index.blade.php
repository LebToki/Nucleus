@extends('layout.layout')

@php
    $title = 'Clienteling & Sales Cockpit';
    $subTitle = 'VIP Accounts & Pipeline';
@endphp

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                <span class="text-xs fw-semibold text-warning-700 text-uppercase">VIP Tier Clients</span>
                <h3 class="fw-bold text-neutral-900 mt-2 mb-1">28</h3>
                <span class="text-xs text-neutral-500">Patron accounts</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                <span class="text-xs fw-semibold text-teal-700 text-uppercase">Sovereign & Enterprise</span>
                <h3 class="fw-bold text-neutral-900 mt-2 mb-1">12</h3>
                <span class="text-xs text-neutral-500">Contracted entities</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                <span class="text-xs fw-semibold text-success-700 text-uppercase">Booked Deal Volume</span>
                <h3 class="fw-bold text-success-600 mt-2 mb-1">$890,240.00</h3>
                <span class="text-xs text-neutral-500">Closed deals</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                <span class="text-xs fw-semibold text-danger-700 text-uppercase">Active Tenders & Quotes</span>
                <h3 class="fw-bold text-danger-600 mt-2 mb-1">$1,420,000.00</h3>
                <span class="text-xs text-neutral-500">Pending proposals</span>
            </div>
        </div>
    </div>
</div>
@endsection
