@extends('layout.layout')

@php
    $title = 'Product Catalog';
    $subTitle = 'Commercial Names & Raw Agarwood Variants';
@endphp

@section('content')
    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="mb-4">Al-Marmouq Product Catalog</h6>
                <p class="text-secondary-light mb-0">Commercial product identity sits above distributor shape, grade, and
                    official supplier lots.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('products.create') }}" class="btn btn-primary-600 radius-8">Add Product</a>
                <a href="{{ route('invoiceAdd') }}" class="btn btn-light radius-8">Build Quote</a>
            </div>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table align-middle">
                    <thead>
                        <tr>
                            <th>Commercial Product</th>
                            <th>Grade</th>
                            <th>Distributor Shape</th>
                            <th>Opening Stock</th>
                            <th>Supplier Value</th>
                            <th>Pricing Rule</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $lot = $product->lots->first();
                                $productKey = match ($product->business_name) {
                                    'Al-Riyassi', 'Al-Riyasi' => 'al_riyassi',
                                    'Al-Nader' => 'al_nader',
                                    'Al-Safwah' => 'al_safwah',
                                    default => 'al_naqwah',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ __('entities.products.' . $productKey) }}</strong>
                                    <span
                                        class="d-block text-secondary-light text-sm">{{ app()->getLocale() === 'ar' ? $product->business_name_ar : $product->business_name }}</span>
                                </td>
                                <td><span class="badge text-primary-600 bg-primary-50">{{ $product->grade }}</span></td>
                                <td>{{ $product->distributor_shape }}</td>
                                <td>{{ number_format((float) $product->lots->sum('quantity_kg'), 4) }} KG</td>
                                <td>AED {{ number_format((float) $product->lots->sum('supplier_amount'), 2) }}</td>
                                <td>{{ (int) round($lot?->target_margin_rate * 100) }}% margin ·
                                    {{ (int) round($lot?->working_cost_rate * 100) }}% working cost</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="bg-success-focus text-success-600 w-40-px h-40-px d-flex align-items-center justify-content-center rounded-circle"
                                            title="Edit product"><iconify-icon icon="lucide:edit"></iconify-icon></a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                            onsubmit="return confirm('Delete this product?')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="bg-danger-focus text-danger-600 w-40-px h-40-px d-flex align-items-center justify-content-center rounded-circle border-0"
                                                title="Delete product"><iconify-icon
                                                    icon="fluent:delete-24-regular"></iconify-icon></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary-light py-32">No products have been
                                    registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
