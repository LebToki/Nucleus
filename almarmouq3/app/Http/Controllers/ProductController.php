<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPriceHistory;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function catalog(): View
    {
        return view('products.catalog', [
            'products' => Product::with('lots')->orderBy('business_name')->orderBy('grade')->get(),
        ]);
    }

    public function priceHistory(): View
    {
        return view('products.price-history', [
            'prices' => ProductPriceHistory::with('product')->latest('valid_from')->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.form', ['product' => new Product()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($request->validate([
            'product_key' => ['required', 'string', 'max:100', 'unique:products,product_key'],
            'business_name' => ['required', 'string', 'max:120'],
            'business_name_ar' => ['required', 'string', 'max:120'],
            'grade' => ['required', 'string', 'max:50'],
            'distributor_shape' => ['required', 'string', 'max:80'],
            'active' => ['nullable', 'boolean'],
        ]));

        return redirect()->route('products.catalog')->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('products.form', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($request->validate([
            'product_key' => ['required', 'string', 'max:100', 'unique:products,product_key,' . $product->id],
            'business_name' => ['required', 'string', 'max:120'],
            'business_name_ar' => ['required', 'string', 'max:120'],
            'grade' => ['required', 'string', 'max:50'],
            'distributor_shape' => ['required', 'string', 'max:80'],
            'active' => ['nullable', 'boolean'],
        ]));

        return redirect()->route('products.catalog')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        abort_if($product->lots()->exists() || $product->priceHistory()->exists(), 409, 'Products with recorded history cannot be deleted.');
        $product->delete();

        return redirect()->route('products.catalog')->with('status', 'Product deleted.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
