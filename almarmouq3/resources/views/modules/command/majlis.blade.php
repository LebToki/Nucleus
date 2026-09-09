@extends('layout.layout')

@php
    $title = __('entities.modules.command');
    $subTitle = __('entities.sidebar.majlis_calendar');
@endphp

@section('content')
<div class="row g-4">
    <!-- Header Summary Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.majlis_calendar') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Executive Majlis Protocol, Session Scheduling & Cultural Protocol Briefing</p>
                </div>
                <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#scheduleMajlisModal">
                    <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
                    <span>Schedule Majlis Session</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Active & Upcoming Sessions -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-header bg-neutral-0 border-bottom border-neutral-200 py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-md fw-semibold text-primary-light mb-0">Scheduled Majlis Sessions</h6>
                <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-4 text-xs fw-semibold">{{ count($sessions ?? []) }} Sessions</span>
            </div>
            <div class="card-body p-24">
                @if(isset($sessions) && count($sessions) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Protocol ID</th>
                                    <th>VIP / Host Entity</th>
                                    <th>Date & Time</th>
                                    <th>Hills & Location</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                    <tr>
                                        <td class="fw-semibold text-primary-600">#MAJ-{{ str_pad($session->id ?? 1, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="w-32-px h-32-px bg-primary-100 text-primary-600 radius-circle d-flex align-items-center justify-content-center fw-bold">
                                                    {{ strtoupper(substr($session->guest_name ?? 'V', 0, 1)) }}
                                                </div>
                                                <span class="fw-medium text-neutral-800">{{ $session->guest_name ?? 'Royal Guest' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-secondary-light">{{ $session->scheduled_at ?? now()->format('Y-m-d H:i') }}</td>
                                        <td>{{ $session->location ?? 'Al-Marmouq Sovereign Lounge' }}</td>
                                        <td>
                                            <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs">Confirmed</span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary radius-6 px-12">View Protocol Brief</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-48">
                        <div class="w-64-px h-64-px bg-neutral-100 radius-circle d-inline-flex align-items-center justify-content-center mb-16 text-primary-600">
                            <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-3xl"></iconify-icon>
                        </div>
                        <h6 class="text-md fw-semibold text-neutral-700 mb-2">No Majlis Sessions Scheduled</h6>
                        <p class="text-sm text-secondary-light mb-16">All upcoming sovereign receptions and majlis briefings will be displayed here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cultural & Protocol Rules Widget -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm radius-12 mb-24">
            <div class="card-header bg-neutral-0 border-bottom border-neutral-200 py-16 px-24">
                <h6 class="text-md fw-semibold text-primary-light mb-0">Majlis Protocol Checklist</h6>
            </div>
            <div class="card-body p-20">
                <ul class="list-group list-group-flush border-0">
                    <li class="list-group-item border-0 px-0 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                            <span class="text-sm fw-medium">Toula & Dehn Oud Samples Prepared</span>
                        </div>
                    </li>
                    <li class="list-group-item border-0 px-0 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                            <span class="text-sm fw-medium">Hospitality & Coffee Protocol Briefed</span>
                        </div>
                    </li>
                    <li class="list-group-item border-0 px-0 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:clock-circle-bold" class="text-warning-600 text-lg"></iconify-icon>
                            <span class="text-sm fw-medium">Bespoke Engraving Certificate Verification</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
