<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class HomeController extends Controller
{
    public function firstshow()
    {
        // 全カテゴリーを取得
        $categories = Category::all();

        return view('dashboard', compact('categories'));
    }
}