<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tile;

class GameController extends Controller
{
    // index ���\�b�h
    public function index()
    {
        $tiles = Tile::where('owner_id', auth()->id())->get();
        return view('game.index', compact('tiles'));
    }

    // battle ���\�b�h�iindex�Ƃ͕ʁj
    public function battle()
    {
        $tiles = Tile::where('owner_id', auth()->id())->get();
        return view('game.battle', compact('tiles'));
    }
}
