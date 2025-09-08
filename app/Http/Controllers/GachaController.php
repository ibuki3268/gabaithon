<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GachaController extends Controller
{
    /**
     * ガチャ画面を表示
     */
    public function index()
    {
        // ユーザーの現在のポイント（仮データ）
        $user = Auth::user();
        $userPoints = $user->points ?? 1000; // usersテーブルのpointsカラムを使用
        
        // ガチャの設定
        $gachaTypes = [
            'single' => [
                'name' => '単発ガチャ',
                'cost' => 100,
                'description' => '1回だけ引けるガチャ'
            ],
            'ten' => [
                'name' => '10連ガチャ',
                'cost' => 900,
                'description' => '10回分まとめて引けるお得なガチャ'
            ]
        ];
        
        // 排出されるアイテム一覧（仮データ）
        $availableItems = [
            ['name' => '麻雀牌・一萬', 'rarity' => 'N', 'rate' => 60],
            ['name' => '麻雀牌・九萬', 'rarity' => 'R', 'rate' => 25],
            ['name' => 'レアキャラ', 'rarity' => 'SR', 'rate' => 12],
            ['name' => '伝説の牌', 'rarity' => 'SSR', 'rate' => 3]
        ];
        
        return view('gacha', compact('userPoints', 'gachaTypes', 'availableItems'));
    }

    /**
     * ガチャを実行
     */
    public function draw(Request $request)
    {
        $user = Auth::user();
        $gachaType = $request->input('gacha_type', 'single');
        
        // ガチャタイプに応じたコストと回数
        $gachaConfig = [
            'single' => ['cost' => 100, 'count' => 1],
            'ten' => ['cost' => 900, 'count' => 10]
        ];
        
        $config = $gachaConfig[$gachaType] ?? $gachaConfig['single'];
        $cost = $config['cost'];
        $drawCount = $config['count'];
        
        // ポイント確認（仮の実装）
        $userPoints = $user->points ?? 1000;
        if ($userPoints < $cost) {
            return redirect()->route('gacha.index')
                ->with('error', 'ポイントが不足しています');
        }
        
        // ガチャ結果生成
        $results = [];
        for ($i = 0; $i < $drawCount; $i++) {
            $results[] = $this->drawSingleItem();
        }
        
        // ポイント消費（実際の実装では DB更新が必要）
        // $user->points -= $cost;
        // $user->save();
        
        return redirect()->route('gacha.result')
            ->with('results', $results)
            ->with('gacha_type', $gachaType);
    }

    /**
     * ガチャ結果を表示
     */
    public function result()
    {
        $results = session('results', []);
        $gachaType = session('gacha_type', 'single');
        
        if (empty($results)) {
            return redirect()->route('gacha.index');
        }
        
        return view('gacha-result', compact('results', 'gachaType'));
    }

    /**
     * 単発ガチャの抽選処理
     */
    private function drawSingleItem()
    {
        // 排出テーブル（レアリティと排出率）
        $itemPool = [
            // N: 60%
            ['name' => '一萬', 'rarity' => 'N', 'type' => 'tile', 'image' => '/images/tiles/1man.png'],
            ['name' => '二萬', 'rarity' => 'N', 'type' => 'tile', 'image' => '/images/tiles/2man.png'],
            ['name' => '三萬', 'rarity' => 'N', 'type' => 'tile', 'image' => '/images/tiles/3man.png'],
            
            // R: 25%
            ['name' => '白', 'rarity' => 'R', 'type' => 'tile', 'image' => '/images/tiles/haku.png'],
            ['name' => '發', 'rarity' => 'R', 'type' => 'tile', 'image' => '/images/tiles/hatsu.png'],
            
            // SR: 12%
            ['name' => '学習サポーター', 'rarity' => 'SR', 'type' => 'character', 'image' => '/images/characters/supporter.png'],
            ['name' => '麻雀マスター', 'rarity' => 'SR', 'type' => 'character', 'image' => '/images/characters/master.png'],
            
            // SSR: 3%
            ['name' => '伝説の牌師', 'rarity' => 'SSR', 'type' => 'character', 'image' => '/images/characters/legend.png']
        ];
        
        // 重み付き抽選
        $weights = [
            'N' => 60,
            'R' => 25, 
            'SR' => 12,
            'SSR' => 3
        ];
        
        $rarity = $this->weightedRandom($weights);
        
        // 該当レアリティのアイテムからランダム選択
        $rarityItems = array_filter($itemPool, function($item) use ($rarity) {
            return $item['rarity'] === $rarity;
        });
        
        return $rarityItems[array_rand($rarityItems)];
    }

    /**
     * 重み付きランダム選択
     */
    private function weightedRandom($weights)
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        
        $current = 0;
        foreach ($weights as $key => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $key;
            }
        }
        
        return array_key_first($weights);
    }
}