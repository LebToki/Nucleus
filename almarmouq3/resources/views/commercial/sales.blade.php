@extends('layout.layout')

@php($title = 'Sales Ledger')
@php($subTitle = 'Recorded Product Sales')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Sales Ledger</h6>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table align-middle">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Subtotal</th>
                            <th>UAE VAT 5%</th>
                            <th>Total</th>
                            <th>Sold At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="fw-semibold">{{ $sale->reference }}</td>
                                <td>{{ $sale->customer->name ?? 'Walk-in / private client' }}</td>
                                <td><span class="badge text-primary-600 bg-primary-50">{{ ucfirst($sale->status) }}</span>
                                </td>
                                <td>AED {{ number_format((float) $sale->subtotal, 2) }}</td>
                                <td>AED {{ number_format((float) $sale->vat_amount, 2) }}</td>
                                <td class="fw-semibold">AED {{ number_format((float) $sale->total_amount, 2) }}</td>
                                <td>{{ optional($sale->sold_at)->format('d M Y H:i') ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary-light py-32">No sales recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
