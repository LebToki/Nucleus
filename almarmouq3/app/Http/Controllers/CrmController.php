<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function index()
    {
        return view('modules.crm.index', [
            'userId' => auth()->id(),
            'stats' => [
                'vip_clients' => User::role('client')->count(),
                'accounts' => Customer::count(),
                'sales_this_month' => Sale::whereMonth('created_at', now()->month)->sum('total'),
            ],
        ]);
    }

    public function vipClients()
    {
        $clients = User::role('client')
            ->withCount('sales')
            ->orderBy('sales_count', 'desc')
            ->get();

        return view('modules.crm.vip-clients', [
            'clients' => $clients,
            'userId' => auth()->id(),
        ]);
    }

    public function accounts()
    {
        $accounts = User::role('client')->get();
        return view('modules.crm.accounts', [
            'accounts' => $accounts,
            'userId' => auth()->id(),
        ]);
    }

    public function tenders()
    {
        return view('modules.crm.tenders', [
            'userId' => auth()->id(),
        ]);
    }

    public function quoteBuilder()
    {
        $customers = Customer::all();
        $products = \App\Models\Product::all();
        return view('modules.crm.quote-builder', [
            'customers' => $customers,
            'products' => $products,
            'userId' => auth()->id(),
        ]);
    }

    public function customers()
    {
        $customers = Customer::all();
        return view('modules.crm.customers', [
            'customers' => $customers,
            'userId' => auth()->id(),
        ]);
    }

    public function sales()
    {
        $sales = Sale::with('customer')->orderBy('created_at', 'desc')->get();
        return view('modules.crm.sales', [
            'sales' => $sales,
            'userId' => auth()->id(),
        ]);
    }

    public function pricing()
    {
        $products = \App\Models\Product::with('priceHistory')->get();
        return view('modules.crm.pricing', [
            'products' => $products,
            'userId' => auth()->id(),
        ]);
    }
}
