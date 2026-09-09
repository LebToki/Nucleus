@extends('layout.layout')

@php
    $title = __('entities.modules.crm');
    $subTitle = __('entities.sidebar.tenders');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.tenders') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Government Tenders, Sovereign Gifting Procurement Bids & Commercial Quotations</p>
                </div>
                <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-add-outline" class="text-xl"></iconify-icon>
                    <span>Submit New Tender Bid</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tenders Board -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tender Ref</th>
                                <th>Issuing Authority / Client</th>
                                <th>Scope of Work</th>
                                <th>Estimated Value</th>
                                <th>Submission Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-primary-600">#TND-2026-04</td>
                                <td>Ministry of Foreign Affairs & Intl Cooperation</td>
                                <td>Diplomatic Royal Gift Sets & Engraved Crystal Decanters</td>
                                <td class="fw-bold text-success-600">AED 2,400,000</td>
                                <td>2026-09-25</td>
                                <td><span class="badge bg-warning-50 text-warning-600 px-10 py-4 radius-4 text-xs">Under Evaluation</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-primary-600">#TND-2026-02</td>
                                <td>Abu Dhabi Executive Office</td>
                                <td>State Reception Oud Incense & Gold Caskets</td>
                                <td class="fw-bold text-success-600">AED 1,150,000</td>
                                <td>2026-08-30</td>
                                <td><span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs">Awarded</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
