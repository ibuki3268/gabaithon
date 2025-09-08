<?php

namespace App\Services;

class MahjongWinChecker
{
    // 牌の並び順を定義
    private $tileOrder = [
        // 萬子
        '一萬' => 1, '二萬' => 2, '三萬' => 3, '四萬' => 4, '五萬' => 5,
        '六萬' => 6, '七萬' => 7, '八萬' => 8, '九萬' => 9,
        // 筒子
        '一筒' => 11, '二筒' => 12, '三筒' => 13, '四筒' => 14, '五筒' => 15,
        '六筒' => 16, '七筒' => 17, '八筒' => 18, '九筒' => 19,
        // 索子
        '一索' => 21, '二索' => 22, '三索' => 23, '四索' => 24, '五索' => 25,
        '六索' => 26, '七索' => 27, '八索' => 28, '九索' => 29,
        // 字牌
        '東' => 31, '南' => 32, '西' => 33, '北' => 34,
        '中' => 35, '白' => 36, '發' => 37
    ];

    /**
     * 勝利判定のメイン関数
     */
    public function checkWin($hand, $winningTile = null)
    {
        // 手牌に勝利牌を追加（ツモまたはロン）
        $fullHand = $hand;
        if ($winningTile) {
            $fullHand[] = $winningTile;
        }

        // 14枚でない場合は和了不可
        if (count($fullHand) !== 14) {
            return false;
        }

        // 牌の枚数をカウント
        $tileCounts = array_count_values($fullHand);

        // 国士無双の判定
        if ($this->checkKokushiMusou($tileCounts)) {
            return [
                'win' => true,
                'type' => '国士無双',
                'han' => 13, // 役満
                'description' => '13種の?九牌を1枚ずつ＋どれか1枚の対子'
            ];
        }

        // 七対子の判定
        if ($this->checkChitoitsu($tileCounts)) {
            return [
                'win' => true,
                'type' => '七対子',
                'han' => 2,
                'description' => '7つの対子'
            ];
        }

        // 基本形（4面子1雀頭）の判定
        $basicWin = $this->checkBasicWin($tileCounts);
        if ($basicWin) {
            return $basicWin;
        }

        return false;
    }

    /**
     * 基本的な和了形（4面子1雀頭）の判定
     */
    private function checkBasicWin($tileCounts)
    {
        // 雀頭（対子）を探す
        foreach ($tileCounts as $tile => $count) {
            if ($count >= 2) {
                // この牌を雀頭として仮定
                $tempCounts = $tileCounts;
                $tempCounts[$tile] -= 2;
                
                // 残りの牌で4面子が作れるかチェック
                if ($this->checkMentsu($tempCounts, 4)) {
                    return [
                        'win' => true,
                        'type' => '基本和了形',
                        'han' => 1, // 最低1翻（門前清自摸和など）
                        'description' => '4面子1雀頭',
                        'head' => $tile
                    ];
                }
            }
        }

        return false;
    }

    /**
     * 面子（順子・刻子）の判定（再帰的）
     */
    private function checkMentsu($tileCounts, $neededMentsu)
    {
        // 必要な面子数が0になれば成功
        if ($neededMentsu === 0) {
            // 残り牌がないかチェック
            foreach ($tileCounts as $count) {
                if ($count > 0) return false;
            }
            return true;
        }

        // 牌が残っていない場合は失敗
        $totalTiles = array_sum($tileCounts);
        if ($totalTiles === 0) return false;

        // 刻子（同じ牌3枚）のチェック
        foreach ($tileCounts as $tile => $count) {
            if ($count >= 3) {
                $tempCounts = $tileCounts;
                $tempCounts[$tile] -= 3;
                if ($this->checkMentsu($tempCounts, $neededMentsu - 1)) {
                    return true;
                }
            }
        }

        // 順子（連続する3牌）のチェック
        $shuntsu = $this->findShuntsu($tileCounts);
        if ($shuntsu) {
            $tempCounts = $tileCounts;
            foreach ($shuntsu as $tile) {
                $tempCounts[$tile]--;
            }
            if ($this->checkMentsu($tempCounts, $neededMentsu - 1)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 順子を探す
     */
    private function findShuntsu($tileCounts)
    {
        // 数牌のみ順子になれる（字牌は不可）
        $suits = [
            ['一萬', '二萬', '三萬', '四萬', '五萬', '六萬', '七萬', '八萬', '九萬'],
            ['一筒', '二筒', '三筒', '四筒', '五筒', '六筒', '七筒', '八筒', '九筒'],
            ['一索', '二索', '三索', '四索', '五索', '六索', '七索', '八索', '九索']
        ];

        foreach ($suits as $suit) {
            for ($i = 0; $i <= 6; $i++) {
                $tile1 = $suit[$i];
                $tile2 = $suit[$i + 1];
                $tile3 = $suit[$i + 2];

                if (isset($tileCounts[$tile1]) && $tileCounts[$tile1] > 0 &&
                    isset($tileCounts[$tile2]) && $tileCounts[$tile2] > 0 &&
                    isset($tileCounts[$tile3]) && $tileCounts[$tile3] > 0) {
                    return [$tile1, $tile2, $tile3];
                }
            }
        }

        return null;
    }

    /**
     * 七対子の判定
     */
    private function checkChitoitsu($tileCounts)
    {
        // 7種類の牌でそれぞれ2枚ずつ
        if (count($tileCounts) !== 7) return false;

        foreach ($tileCounts as $count) {
            if ($count !== 2) return false;
        }

        return true;
    }

    /**
     * 国士無双の判定
     */
    private function checkKokushiMusou($tileCounts)
    {
        $yaochuuTiles = ['一萬', '九萬', '一筒', '九筒', '一索', '九索', '東', '南', '西', '北', '中', '白', '發'];

        // 13種類の?九牌のうち12種類は1枚ずつ、1種類は2枚
        $singleCount = 0;
        $pairCount = 0;

        foreach ($yaochuuTiles as $tile) {
            $count = $tileCounts[$tile] ?? 0;
            if ($count === 1) {
                $singleCount++;
            } elseif ($count === 2) {
                $pairCount++;
            } elseif ($count > 2 || $count === 0) {
                return false;
            }
        }

        // 其他牌不能有
        foreach ($tileCounts as $tile => $count) {
            if (!in_array($tile, $yaochuuTiles) && $count > 0) {
                return false;
            }
        }

        return $singleCount === 12 && $pairCount === 1;
    }

    /**
     * 簡単な役判定
     */
    public function checkYaku($hand, $winningTile, $isTsumo = false, $isRiichi = false)
    {
        $yaku = [];
        $totalHan = 0;

        // 門前清自摸和
        if ($isTsumo) {
            $yaku[] = ['name' => '門前清自摸和', 'han' => 1];
            $totalHan += 1;
        }

        // 立直
        if ($isRiichi) {
            $yaku[] = ['name' => '立直', 'han' => 1];
            $totalHan += 1;
        }

        // 断幺九（タンヤオ）
        if ($this->checkTanyao($hand, $winningTile)) {
            $yaku[] = ['name' => '断幺九', 'han' => 1];
            $totalHan += 1;
        }

        // 平和
        if ($this->checkPinfu($hand, $winningTile)) {
            $yaku[] = ['name' => '平和', 'han' => 1];
            $totalHan += 1;
        }

        return [
            'yaku' => $yaku,
            'totalHan' => $totalHan,
            'score' => $this->calculateScore($totalHan)
        ];
    }

    /**
     * 断幺九の判定
     */
    private function checkTanyao($hand, $winningTile)
    {
        $yaochuuTiles = ['一萬', '九萬', '一筒', '九筒', '一索', '九索', '東', '南', '西', '北', '中', '白', '發'];
        
        $fullHand = array_merge($hand, [$winningTile]);
        
        foreach ($fullHand as $tile) {
            if (in_array($tile, $yaochuuTiles)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * 平和の判定（簡略版）
     */
    private function checkPinfu($hand, $winningTile)
    {
        // 簡略実装：字牌がなく、すべて順子の場合
        $tileCounts = array_count_values(array_merge($hand, [$winningTile]));
        
        // 字牌チェック
        $jihai = ['東', '南', '西', '北', '中', '白', '發'];
        foreach ($jihai as $tile) {
            if (isset($tileCounts[$tile]) && $tileCounts[$tile] > 0) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * 点数計算（簡略版）
     */
    private function calculateScore($han)
    {
        $baseScore = [
            1 => 1000,
            2 => 2000,
            3 => 4000,
            4 => 8000,
            5 => 8000, // 満貫
            6 => 12000, // 跳満
            7 => 12000,
            8 => 16000, // 倍満
            9 => 16000,
            10 => 16000,
            11 => 24000, // 三倍満
            12 => 24000,
            13 => 32000, // 役満
        ];

        return $baseScore[$han] ?? 32000;
    }
}