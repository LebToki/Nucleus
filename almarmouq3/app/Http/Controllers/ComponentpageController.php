<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComponentpageController extends Controller
{
    public function alert()
    {
        return view('componentspage/alert');
    }
    
    public function avatar()
    {
        return view('componentspage/avatar');
    }
    
    public function badges()
    {
        return view('componentspage/badges');
    }
    
    public function button()
    {
        return view('componentspage/button');
    }
    
    public function calendar()
    {
        return view('calendar');
    }
    
    public function card()
    {
        return view('componentspage/card');
    }
    
    public function carousel()
    {
        return view('componentspage/carousel');
    }
    
    public function colors()
    {
        return view('componentspage/colors');
    }
    
    public function dropdown()
    {
        return view('componentspage/dropdown');
    }
    
    public function imageUpload()
    {
        return view('componentspage/imageUpload');
    }
    
    public function list()
    {
        return view('componentspage/list');
    }
    
    public function pagination()
    {
        return view('componentspage/pagination');
    }
    
    public function progress()
    {
        return view('componentspage/progress');
    }
    
    public function radio()
    {
        return view('componentspage/radio');
    }
    
    public function starRating()
    {
        return view('componentspage/starRating');
    }
    
    public function switch()
    {
        return view('componentspage/switch');
    }
    
    public function tabs()
    {
        return view('componentspage/tabs');
    }
    
    public function tags()
    {
        return view('componentspage/tags');
    }
    
    public function tooltip()
    {
        return view('componentspage/tooltip');
    }
    
    public function typography()
    {
        return view('componentspage/typography');
    }
    
    public function videos()
    {
        return view('componentspage/videos');
    }
    
}
