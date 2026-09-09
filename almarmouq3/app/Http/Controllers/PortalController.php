<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        return view('modules.portal.index', [
            'userId' => auth()->id(),
            'stats' => [
                'my_orders' => Sale::where('customer_id', auth()->id())->count(),
                'my_allocations' => 0,
            ],
        ]);
    }

    public function allocations()
    {
        $allocations = Sale::where('customer_id', auth()->id())->get();
        return view('modules.portal.allocations', [
            'allocations' => $allocations,
            'userId' => auth()->id(),
        ]);
    }

    public function catalog()
    {
        $products = \App\Models\Product::all();
        return view('modules.portal.catalog', [
            'products' => $products,
            'userId' => auth()->id(),
        ]);
    }

    public function bespokeStudio()
    {
        return view('modules.portal.bespoke-studio', [
            'userId' => auth()->id(),
        ]);
    }

    public function orders()
    {
        $orders = Sale::where('customer_id', auth()->id())->orderBy('created_at', 'desc')->get();
        return view('modules.portal.orders', [
            'orders' => $orders,
            'userId' => auth()->id(),
        ]);
    }

    public function tracking()
    {
        return view('modules.portal.tracking', [
            'userId' => auth()->id(),
        ]);
    }

    public function certificates()
    {
        return view('modules.portal.certificates', [
            'userId' => auth()->id(),
        ]);
    }
}
