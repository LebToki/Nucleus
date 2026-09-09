@extends('layout.layout')

@php
    $title = __('entities.modules.financial');
    $subTitle = __('entities.sidebar.tax_settlements');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.tax_settlements') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">UAE Corporate Tax & Federal Tax Authority (FTA) VAT Returns & Exemptions</p>
                </div>
                <button type="button" class="btn btn-outline-primary radius-8 px-16 py-9">Download FTA Audit File (FAF)</button>
            </div>
        </div>
    </div>

    <!-- Tax Overview -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm radius-12 p-24">
            <h6 class="text-md fw-semibold text-primary-light mb-16">UAE VAT Return Summary (Q3 2026)</h6>
            <div class="d-flex justify-content-between py-12 border-bottom border-neutral-200">
                <span class="text-sm text-secondary-light">Standard Rated Supplies (5%)</span>
                <span class="text-sm fw-bold">AED 3,800,000</span>
            </div>
            <div class="d-flex justify-content-between py-12 border-bottom border-neutral-200">
                <span class="text-sm text-secondary-light">Output VAT Collected</span>
                <span class="text-sm fw-bold text-success-600">AED 190,000</span>
            </div>
            <div class="d-flex justify-content-between py-12 border-bottom border-neutral-200">
                <span class="text-sm text-secondary-light">Input VAT Recoverable</span>
                <span class="text-sm fw-bold text-danger-600">AED 68,000</span>
            </div>
            <div class="d-flex justify-content-between py-12">
                <span class="text-sm fw-bold text-primary-600">Net VAT Payable</span>
                <span class="text-sm fw-bold text-primary-600">AED 122,000</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm radius-12 p-24">
            <h6 class="text-md fw-semibold text-primary-light mb-16">Corporate Tax Status (9%)</h6>
            <div class="d-flex justify-content-between py-12 border-bottom border-neutral-200">
                <span class="text-sm text-secondary-light">Taxable Net Profit</span>
                <span class="text-sm fw-bold">AED 1,775,000</span>
            </div>
            <div class="d-flex justify-content-between py-12 border-bottom border-neutral-200">
                <span class="text-sm text-secondary-light">Statutory Threshold Exemption</span>
                <span class="text-sm fw-bold text-success-600">AED 375,000</span>
            </div>
            <div class="d-flex justify-content-between py-12">
                <span class="text-sm fw-bold text-primary-600">Estimated Corporate Tax</span>
                <span class="text-sm fw-bold text-primary-600">AED 126,000</span>
            </div>
        </div>
    </div>
</div>
@endsection
