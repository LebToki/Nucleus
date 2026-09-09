@extends('layout.layout')

@php
    $title = __('entities.modules.crm');
    $subTitle = __('entities.sidebar.vip_clients');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.vip_clients') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Royal Protocol Dossiers, Bespoke Preferences, Family Crest Allocations & Preferred Oud Profiles</p>
                </div>
                <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:crown-line-duotone" class="text-xl"></iconify-icon>
                    <span>Register VIP Client Dossier</span>
                </button>
            </div>
        </div>
    </div>

    <!-- VIP Client Grid -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm radius-12 p-24 text-center">
            <div class="w-80-px h-80-px bg-primary-100 text-primary-600 radius-circle d-inline-flex align-items-center justify-content-center fw-bold text-2xl mb-16 mx-auto">
                H.H.
            </div>
            <h5 class="text-md fw-bold text-neutral-800 mb-4">H.H. Sheikh Al-Maktoum House</h5>
            <span class="badge bg-warning-50 text-warning-700 px-12 py-4 radius-4 text-xs fw-semibold mb-16">Royal Protocol Tier I</span>
            <p class="text-xs text-secondary-light mb-16">Preferred Notes: Vintage Cambodi Oil, Wild Koh Kong Wood (1984 Curing)</p>
            <div class="d-flex justify-content-between border-top border-neutral-200 pt-16">
                <span class="text-xs text-secondary-light">Lifetime Allocation</span>
                <span class="text-xs fw-bold text-primary-600">AED 1,850,000</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm radius-12 p-24 text-center">
            <div class="w-80-px h-80-px bg-success-100 text-success-600 radius-circle d-inline-flex align-items-center justify-content-center fw-bold text-2xl mb-16 mx-auto">
                H.E.
            </div>
            <h5 class="text-md fw-bold text-neutral-800 mb-4">H.E. Minister of Protocol</h5>
            <span class="badge bg-primary-50 text-primary-600 px-12 py-4 radius-4 text-xs fw-semibold mb-16">Diplomatic Protocol Tier II</span>
            <p class="text-xs text-secondary-light mb-16">Preferred Notes: Taifi Rose & Royal Amber Blends</p>
            <div class="d-flex justify-content-between border-top border-neutral-200 pt-16">
                <span class="text-xs text-secondary-light">Lifetime Allocation</span>
                <span class="text-xs fw-bold text-primary-600">AED 940,000</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm radius-12 p-24 text-center">
            <div class="w-80-px h-80-px bg-info-100 text-info-600 radius-circle d-inline-flex align-items-center justify-content-center fw-bold text-2xl mb-16 mx-auto">
                VIP
            </div>
            <h5 class="text-md fw-bold text-neutral-800 mb-4">Private Collector Vault #09</h5>
            <span class="badge bg-neutral-100 text-neutral-800 px-12 py-4 radius-4 text-xs fw-semibold mb-16">Bespoke Vault Collector</span>
            <p class="text-xs text-secondary-light mb-16">Preferred Notes: Aged Trat Oils & Gold-Embossed Decanters</p>
            <div class="d-flex justify-content-between border-top border-neutral-200 pt-16">
                <span class="text-xs text-secondary-light">Lifetime Allocation</span>
                <span class="text-xs fw-bold text-primary-600">AED 620,000</span>
            </div>
        </div>
    </div>
</div>
@endsection
