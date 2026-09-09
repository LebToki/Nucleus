@extends('layout.layout')

@php
    $title = $product->exists ? 'Edit Product' : 'Add Product';
    $subTitle = 'Product Catalog';
@endphp

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">{{ $product->exists ? 'Edit Product Variant' : 'Add Product Variant' }}</h6>
        </div>
        <div class="card-body p-24">
            <form action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}"
                method="POST">
                @csrf
                @if ($product->exists)
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-20"><label class="form-label">Product key</label><input name="product_key"
                                value="{{ old('product_key', $product->product_key) }}" class="form-control" required>
                            @error('product_key')
                                <div class="text-danger-main text-sm mt-8">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-20"><label class="form-label">Distributor shape</label><input
                                name="distributor_shape" value="{{ old('distributor_shape', $product->distributor_shape) }}"
                                class="form-control" placeholder="Jurra, Murri, Salla" required></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-20"><label class="form-label">Commercial name (EN)</label><input
                                name="business_name" value="{{ old('business_name', $product->business_name) }}"
                                class="form-control" placeholder="Al-Riyassi" required></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-20"><label class="form-label">Commercial name (AR)</label><input
                                name="business_name_ar" value="{{ old('business_name_ar', $product->business_name_ar) }}"
                                class="form-control" dir="rtl" placeholder="الرئاسي" required></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-20"><label class="form-label">Grade</label><input name="grade"
                                value="{{ old('grade', $product->grade) }}" class="form-control"
                                placeholder="Super, A+, A, B+, B, C" required></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-20"><label class="form-label d-block">Status</label><label
                                class="d-flex align-items-center gap-2"><input type="checkbox" name="active" value="1"
                                    {{ old('active', $product->exists ? $product->active : true) ? 'checked' : '' }}> Active
                                in catalog</label></div>
                    </div>
                </div>
                <div class="d-flex gap-3"><a href="{{ route('products.catalog') }}" class="btn btn-light">Cancel</a><button
                        class="btn btn-primary-600"
                        type="submit">{{ $product->exists ? 'Save Changes' : 'Create Product' }}</button></div>
            </form>
        </div>
    </div>
@endsection
