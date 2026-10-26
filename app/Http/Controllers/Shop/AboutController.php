<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    public function index()
    {
        return view('shop.about');
    }
} 