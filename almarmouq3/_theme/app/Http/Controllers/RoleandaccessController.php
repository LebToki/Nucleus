<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleandaccessController extends Controller
{
    public function assignRole()
    {
        return view('roleandaccess/assignRole');
    }
    
    public function roleAaccess()
    {
        return view('roleandaccess/roleAaccess');
    }
}
