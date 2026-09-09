<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\View\View;

class CommercialController extends Controller
{
    public function customers(): View
    {
        return view('commercial.customers', ['customers' => Customer::latest()->get()]);
    }

    public function sales(): View
    {
        return view('commercial.sales', ['sales' => Sale::with('customer', 'items.product')->latest()->get()]);
    }
}
