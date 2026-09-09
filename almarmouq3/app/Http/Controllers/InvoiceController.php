<?php

namespace App\Http\Controllers;

use App\Models\ProductLot;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function invoiceAdd()
    {
        return view('invoice/invoiceAdd');
    }

    public function quoteBuilder()
    {
        return view('quotes.builder', [
            'lots' => ProductLot::query()
                ->where('source_reference', 'GCC-BLR-01')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function invoiceEdit()
    {
        return view('invoice/invoiceEdit');
    }

    public function invoiceList()
    {
        return view('invoice/invoiceList');
    }

    public function invoicePreview()
    {
        return view('invoice/invoicePreview');
    }
}
