@extends('layout.layout')

@php($title = 'Price History')
@php($subTitle = 'Product Pricing Control')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Commercial Price History</h6>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Package</th>
                            <th>Weight</th>
                            <th>Price</th>
                            <th>Valid From</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prices as $price)
                            <tr>
                                <td>{{ $price->product->business_name ?? 'Unknown product' }} ·
                                    {{ $price->product->grade ?? '' }}</td>
                                <td>{{ $price->package_name }}</td>
                                <td>{{ number_format((float) $price->weight_kg, 6) }} KG</td>
                                <td class="fw-semibold">{{ $price->currency }}
                                    {{ number_format((float) $price->unit_price, 2) }}</td>
                                <td>{{ optional($price->valid_from)->format('d M Y') }}</td>
                                <td>{{ ucfirst($price->source) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary-light py-32">No package prices recorded
                                    yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
