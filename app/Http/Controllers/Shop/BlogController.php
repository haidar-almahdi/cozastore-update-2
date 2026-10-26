<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return view('shop.blog.index');
    }

    public function show($post)
    {
        return view('shop.blog.show', compact('post'));
    }
} 