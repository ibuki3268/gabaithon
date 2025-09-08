<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tile;

class GameController extends Controller
{
    // index メソッド
    public function index()
    {
        $tiles = Tile::where('owner_id', auth()->id())->get();
        return view('game.index', compact('tiles'));
    }

    // battle メソッド（indexとは別）
    public function battle()
    {
        $tiles = Tile::where('owner_id', auth()->id())->get();
        return view('game.battle', compact('tiles'));
    }
}
