@extends('layout.layout')

@php($title = 'Customers')
@php($subTitle = 'CRM & Client Dossiers')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between gap-3">
            <h6 class="mb-0">Customer Directory</h6><a href="{{ route('usersList') }}"
                class="btn btn-primary-600 btn-sm">Open CRM</a>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table align-middle">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>TRN</th>
                            <th>Credit Terms</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="fw-semibold">{{ $customer->name }}</td>
                                <td>{{ strtoupper($customer->type) }}</td>
                                <td>{{ $customer->email ?: '—' }}</td>
                                <td>{{ $customer->trn ?: '—' }}</td>
                                <td>{{ $customer->credit_terms ?: 'Prepaid' }}</td>
                                <td><span
                                        class="badge text-success-main bg-success-focus">{{ $customer->active ? 'Active' : 'Inactive' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary-light py-32">No customer dossiers
                                    recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
