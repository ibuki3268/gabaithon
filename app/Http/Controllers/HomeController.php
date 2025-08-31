<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tile; // Tileモデルを使うための宣言を追加

class HomeController extends Controller
{
    /**
     * アプリケーションのダッシュボードを表示する
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Tileモデルを使って、tilesテーブルから全てのデータを取得する
        $tiles = Tile::all();

        // 取得したデータを'tiles'という名前でビューに渡す
        return view('dashboard', ['tiles' => $tiles]);
    }
}
