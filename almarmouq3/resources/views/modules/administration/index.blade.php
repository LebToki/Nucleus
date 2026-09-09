@extends('layouts.app')

@section('content')
<div class="d-flex flex-column gap-4">

    {{-- Section Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1">System Governance & Settings Cockpit</h4>
            <p class="text-neutral-500 text-sm mb-0">Identity access management (RBAC), commercial rule governance, security audits & platform configs</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-neutral-200 text-neutral-800 px-3 py-2 radius-8">
                <iconify-icon icon="solar:shield-warning-outline" class="me-1 align-middle text-primary-600"></iconify-icon>
                Security Integrity: Normal
            </span>
        </div>
    </div>

    {{-- Tier 1: 4 KPI Cards --}}
    <div class="row g-3">
        {{-- Card 1: Active Accounts & Assigned Roles --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-warning-700 text-uppercase">Active Personnel</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">24</h3>
                        <span class="text-xs text-neutral-500">Across 4 defined RBAC roles</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-warning-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:user-speak-rounded-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Global Commercial Rules Active --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-teal-700 text-uppercase">Margin & Tier Policies</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">16 Rules</h3>
                        <span class="text-xs text-neutral-500">Tier limits, discount floors & VAT</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-teal-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:calculator-minimalistic-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Security Clearances ("What Is Authorized") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-success-700 text-uppercase">Access Grants (24h)</span>
                        <h3 class="fw-bold text-success-600 mt-2 mb-1">1,420</h3>
                        <span class="text-xs text-neutral-500">100% policy-compliant requests</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-success-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:lock-unlocked-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Security Flags / Audit Alerts ("What Is Flagged / Pending") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-danger-700 text-uppercase">Security Exceptions</span>
                        <h3 class="fw-bold text-danger-600 mt-2 mb-1">3 Alerts</h3>
                        <span class="text-xs text-neutral-500">Failed authentications / IP shifts</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-danger-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:shield-cross-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tier 2: 2 Telemetry Visuals --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">System Auth & Role-Usage Velocity</h6>
                    <span class="text-xs text-neutral-400">Logins vs RBAC Permission Checks</span>
                </div>
                <div id="chart-audit-velocity" style="min-height: 220px;"></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Mail Service Health & SMTP Queues</h6>
                    <span class="text-xs text-neutral-400">Concierge Mailers, Invoices & Dispatch Notices</span>
                </div>
                <div id="chart-mail-queues" style="min-height: 220px;"></div>
            </div>
        </div>
    </div>

    {{-- Tier 3: 2 Telemetry Feeds --}}
    <div class="row g-3">
        {{-- Feed 1: Critical Activity & Security Audit Trail --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Immutable Audit Trail</h6>
                    <a href="{{ route('admin.audit') }}" class="text-xs text-primary-600 fw-semibold text-decoration-none">Full Ledger</a>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($recentAudits ?? [] as $audit)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center bg-neutral-100 text-neutral-800 flex-shrink-0">
                                <iconify-icon icon="{{ $audit->event_icon ?? 'solar:history-outline' }}" class="text-lg"></iconify-icon>
                            </div>
                            <div>
                                <div class="fw-bold text-neutral-900 text-sm">{{ $audit->action_name }}</div>
                                <div class="text-xs text-neutral-500 mt-1">User: <strong>{{ $audit->user_name }}</strong> ({{ $audit->role_title }}) &bull; IP: {{ $audit->ip_address }}</div>
                                <div class="text-xs text-neutral-400 mt-1">{{ $audit->created_at->format('Y-m-d H:i:s') }}</div>
                            </div>
                        </div>
                        <span class="badge {{ $audit->severity === 'warning' ? 'bg-warning-50 text-warning-700' : 'bg-neutral-100 text-neutral-600' }} text-xs px-2 py-1">
                            {{ $audit->module_scope }}
                        </span>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No recent audit logs available.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Feed 2: Commercial Rules, Margins & Policy State --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Active Commercial Margins & Limits</h6>
                    <a href="{{ route('admin.commercial-rules') }}" class="text-xs text-primary-600 fw-semibold text-decoration-none">Manage Rules</a>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($activePolicies ?? [] as $policy)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $policy->rule_title }}</div>
                            <div class="text-xs text-neutral-500 mt-1">Scope: {{ $policy->module_target }} &bull; Max Disc: {{ $policy->max_discount_pct }}% &bull; Min Margin: {{ $policy->min_margin_pct }}%</div>
                            <div class="text-xs text-neutral-400 mt-1">Last modified by: {{ $policy->updated_by_name }}</div>
                        </div>
                        <span class="badge bg-success-50 text-success-600 text-xs px-2 py-1">
                            Active
                        </span>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">Default baseline margin policies applied.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection