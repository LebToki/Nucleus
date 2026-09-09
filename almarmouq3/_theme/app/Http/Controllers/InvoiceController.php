<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function invoiceAdd()
    {
        return view('invoice/invoiceAdd');
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
