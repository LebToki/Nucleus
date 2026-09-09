@extends('layout.layout')

@php
    $title = __('entities.modules.vault_atelier');
    $subTitle = __('entities.sidebar.finished_sets');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.finished_sets') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Numbered Caskets, Sealed Bottled Oils, Finished Gift Sets & Serialized Vault Stock</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Finished Editions Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ed. Code</th>
                                <th>Edition Title</th>
                                <th>Components Included</th>
                                <th>Stock Status</th>
                                <th>Retail Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-primary-600">#ED-SOV-01</td>
                                <td>Royal Sovereign Box Set 2026</td>
                                <td>3 Tolas Cambodi 1984 + Gold Incense Burner</td>
                                <td><span class="badge bg-success-50 text-success-600">4 Sets Available</span></td>
                                <td class="fw-bold">AED 165,000</td>
                                <td><button class="btn btn-sm btn-outline-primary radius-6">Inspect Casket</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
