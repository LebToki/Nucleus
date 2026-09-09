@extends('layout.layout')

@php
    $title = __('entities.modules.crm');
    $subTitle = __('entities.sidebar.customers');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.customers') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Client Directory, Patron Profiles, Order History & Sanctuary Access Permissions</p>
                </div>
                <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                    <iconify-icon icon="flowbite:users-group-outline" class="text-xl"></iconify-icon>
                    <span>Register New Client</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Directory Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Client Name</th>
                                <th>Email / Contact</th>
                                <th>Country / Residence</th>
                                <th>Preferred Collection</th>
                                <th>Total Purchases</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($customers) && count($customers) > 0)
                                @foreach($customers as $c)
                                    <tr>
                                        <td class="fw-semibold text-neutral-800">{{ $c->name ?? 'Client' }}</td>
                                        <td class="text-secondary-light">{{ $c->email ?? 'N/A' }}</td>
                                        <td>{{ $c->country ?? 'United Arab Emirates' }}</td>
                                        <td><span class="badge bg-primary-50 text-primary-600">Royal Dehn Oud</span></td>
                                        <td class="fw-bold">AED {{ number_format($c->total_spent ?? 150000) }}</td>
                                        <td><span class="badge bg-success-50 text-success-600">Active</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="fw-semibold text-neutral-800">Tariq Al-Mansoori</td>
                                    <td class="text-secondary-light">tariq@almansoori.ae</td>
                                    <td>Abu Dhabi, UAE</td>
                                    <td><span class="badge bg-primary-50 text-primary-600">Cambodi Aged Oils</span></td>
                                    <td class="fw-bold">AED 340,000</td>
                                    <td><span class="badge bg-success-50 text-success-600">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-neutral-800">Countess Sofia von Bern</td>
                                    <td class="text-secondary-light">sofia@bern-estate.ch</td>
                                    <td>Geneva, Switzerland</td>
                                    <td><span class="badge bg-warning-50 text-warning-700">Taifi Rose & Crystal</span></td>
                                    <td class="fw-bold">AED 210,000</td>
                                    <td><span class="badge bg-success-50 text-success-600">Active</span></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
