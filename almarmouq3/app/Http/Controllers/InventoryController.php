<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\RawInventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return view('modules.vault_atelier.index', [
            'userId' => auth()->id(),
            'stats' => [
                'products' => Product::count(),
                'raw_items' => RawInventoryItem::count(),
                'total_value' => Product::sum('cost') + RawInventoryItem::sum('unit_cost'),
            ],
        ]);
    }

    public function finishedEditions()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('modules.vault_atelier.finished-editions', [
            'products' => $products,
            'userId' => auth()->id(),
        ]);
    }

    public function catalog()
    {
        $products = Product::with('priceHistory')->orderBy('name')->get();
        return view('modules.vault_atelier.catalog', [
            'products' => $products,
            'userId' => auth()->id(),
        ]);
    }

    public function priceHistory()
    {
        $history = ProductPriceHistory::with('product')->orderBy('created_at', 'desc')->get();
        return view('modules.vault_atelier.price-history', [
            'history' => $history,
            'userId' => auth()->id(),
        ]);
    }

    public function customSets()
    {
        return view('modules.vault_atelier.custom-sets', [
            'userId' => auth()->id(),
        ]);
    }

    public function waitingList()
    {
        $waitingList = \App\Models\User::role('client')->get();
        return view('modules.vault_atelier.waiting-list', [
            'waitingList' => $waitingList,
            'userId' => auth()->id(),
        ]);
    }

    public function coa()
    {
        $coas = Product::whereNotNull('certificate_of_authenticity')->get();
        return view('modules.vault_atelier.coa', [
            'coas' => $coas,
            'userId' => auth()->id(),
        ]);
    }
}
