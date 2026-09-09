<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function tableBasic()
    {
        return view('table/tableBasic');
    }
    
    public function tableData()
    {
        return view('table/tableData');
    }
    
}
