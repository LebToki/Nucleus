<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormsController extends Controller
{
    public function formLayout()
    {
        return view('forms/formLayout');
    }
    
    public function formValidation()
    {
        return view('forms/formValidation');
    }
    
    public function form()
    {
        return view('forms/form');
    }
    
    public function wizard()
    {
        return view('forms/wizard');
    }
    
}
