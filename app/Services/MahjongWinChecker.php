<?php

namespace App\Services;

class MahjongWinChecker
{
    // �v�̕��я����`
    private $tileOrder = [
        // �ݎq
        '����' => 1, '����' => 2, '�O��' => 3, '�l��' => 4, '����' => 5,
        '�Z��' => 6, '����' => 7, '����' => 8, '����' => 9,
        // ���q
        '�ꓛ' => 11, '��' => 12, '�O��' => 13, '�l��' => 14, '�ܓ�' => 15,
        '�Z��' => 16, '����' => 17, '����' => 18, '�㓛' => 19,
        // ���q
        '���' => 21, '���' => 22, '�O��' => 23, '�l��' => 24, '�܍�' => 25,
        '�Z��' => 26, '����' => 27, '����' => 28, '���' => 29,
        // ���v
        '��' => 31, '��' => 32, '��' => 33, '�k' => 34,
        '��' => 35, '��' => 36, '�' => 37
    ];

    /**
     * ��������̃��C���֐�
     */
    public function checkWin($hand, $winningTile = null)
    {
        // ��v�ɏ����v��ǉ��i�c���܂��̓����j
        $fullHand = $hand;
        if ($winningTile) {
            $fullHand[] = $winningTile;
        }

        // 14���łȂ��ꍇ�͘a���s��
        if (count($fullHand) !== 14) {
            return false;
        }

        // �v�̖������J�E���g
        $tileCounts = array_count_values($fullHand);

        // ���m���o�̔���
        if ($this->checkKokushiMusou($tileCounts)) {
            return [
                'win' => true,
                'type' => '���m���o',
                'han' => 13, // ��
                'description' => '13���?��v��1�����{�ǂꂩ1���̑Ύq'
            ];
        }

        // ���Ύq�̔���
        if ($this->checkChitoitsu($tileCounts)) {
            return [
                'win' => true,
                'type' => '���Ύq',
                'han' => 2,
                'description' => '7�̑Ύq'
            ];
        }

        // ��{�`�i4�ʎq1�����j�̔���
        $basicWin = $this->checkBasicWin($tileCounts);
        if ($basicWin) {
            return $basicWin;
        }

        return false;
    }

    /**
     * ��{�I�Șa���`�i4�ʎq1�����j�̔���
     */
    private function checkBasicWin($tileCounts)
    {
        // �����i�Ύq�j��T��
        foreach ($tileCounts as $tile => $count) {
            if ($count >= 2) {
                // ���̔v�𐝓��Ƃ��ĉ���
                $tempCounts = $tileCounts;
                $tempCounts[$tile] -= 2;
                
                // �c��̔v��4�ʎq�����邩�`�F�b�N
                if ($this->checkMentsu($tempCounts, 4)) {
                    return [
                        'win' => true,
                        'type' => '��{�a���`',
                        'han' => 1, // �Œ�1�|�i��O�����̘a�Ȃǁj
                        'description' => '4�ʎq1����',
                        'head' => $tile
                    ];
                }
            }
        }

        return false;
    }

    /**
     * �ʎq�i���q�E���q�j�̔���i�ċA�I�j
     */
    private function checkMentsu($tileCounts, $neededMentsu)
    {
        // �K�v�Ȗʎq����0�ɂȂ�ΐ���
        if ($neededMentsu === 0) {
            // �c��v���Ȃ����`�F�b�N
            foreach ($tileCounts as $count) {
                if ($count > 0) return false;
            }
            return true;
        }

        // �v���c���Ă��Ȃ��ꍇ�͎��s
        $totalTiles = array_sum($tileCounts);
        if ($totalTiles === 0) return false;

        // ���q�i�����v3���j�̃`�F�b�N
        foreach ($tileCounts as $tile => $count) {
            if ($count >= 3) {
                $tempCounts = $tileCounts;
                $tempCounts[$tile] -= 3;
                if ($this->checkMentsu($tempCounts, $neededMentsu - 1)) {
                    return true;
                }
            }
        }

        // ���q�i�A������3�v�j�̃`�F�b�N
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
     * ���q��T��
     */
    private function findShuntsu($tileCounts)
    {
        // ���v�̂ݏ��q�ɂȂ��i���v�͕s�j
        $suits = [
            ['����', '����', '�O��', '�l��', '����', '�Z��', '����', '����', '����'],
            ['�ꓛ', '��', '�O��', '�l��', '�ܓ�', '�Z��', '����', '����', '�㓛'],
            ['���', '���', '�O��', '�l��', '�܍�', '�Z��', '����', '����', '���']
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
     * ���Ύq�̔���
     */
    private function checkChitoitsu($tileCounts)
    {
        // 7��ނ̔v�ł��ꂼ��2������
        if (count($tileCounts) !== 7) return false;

        foreach ($tileCounts as $count) {
            if ($count !== 2) return false;
        }

        return true;
    }

    /**
     * ���m���o�̔���
     */
    private function checkKokushiMusou($tileCounts)
    {
        $yaochuuTiles = ['����', '����', '�ꓛ', '�㓛', '���', '���', '��', '��', '��', '�k', '��', '��', '�'];

        // 13��ނ�?��v�̂���12��ނ�1�����A1��ނ�2��
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

        // �����v�s�\�L
        foreach ($tileCounts as $tile => $count) {
            if (!in_array($tile, $yaochuuTiles) && $count > 0) {
                return false;
            }
        }

        return $singleCount === 12 && $pairCount === 1;
    }

    /**
     * �ȒP�Ȗ𔻒�
     */
    public function checkYaku($hand, $winningTile, $isTsumo = false, $isRiichi = false)
    {
        $yaku = [];
        $totalHan = 0;

        // ��O�����̘a
        if ($isTsumo) {
            $yaku[] = ['name' => '��O�����̘a', 'han' => 1];
            $totalHan += 1;
        }

        // ����
        if ($isRiichi) {
            $yaku[] = ['name' => '����', 'han' => 1];
            $totalHan += 1;
        }

        // �f���i�^�����I�j
        if ($this->checkTanyao($hand, $winningTile)) {
            $yaku[] = ['name' => '�f���', 'han' => 1];
            $totalHan += 1;
        }

        // ���a
        if ($this->checkPinfu($hand, $winningTile)) {
            $yaku[] = ['name' => '���a', 'han' => 1];
            $totalHan += 1;
        }

        return [
            'yaku' => $yaku,
            'totalHan' => $totalHan,
            'score' => $this->calculateScore($totalHan)
        ];
    }

    /**
     * �f���̔���
     */
    private function checkTanyao($hand, $winningTile)
    {
        $yaochuuTiles = ['����', '����', '�ꓛ', '�㓛', '���', '���', '��', '��', '��', '�k', '��', '��', '�'];
        
        $fullHand = array_merge($hand, [$winningTile]);
        
        foreach ($fullHand as $tile) {
            if (in_array($tile, $yaochuuTiles)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * ���a�̔���i�ȗ��Łj
     */
    private function checkPinfu($hand, $winningTile)
    {
        // �ȗ������F���v���Ȃ��A���ׂď��q�̏ꍇ
        $tileCounts = array_count_values(array_merge($hand, [$winningTile]));
        
        // ���v�`�F�b�N
        $jihai = ['��', '��', '��', '�k', '��', '��', '�'];
        foreach ($jihai as $tile) {
            if (isset($tileCounts[$tile]) && $tileCounts[$tile] > 0) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * �_���v�Z�i�ȗ��Łj
     */
    private function calculateScore($han)
    {
        $baseScore = [
            1 => 1000,
            2 => 2000,
            3 => 4000,
            4 => 8000,
            5 => 8000, // ����
            6 => 12000, // ����
            7 => 12000,
            8 => 16000, // �{��
            9 => 16000,
            10 => 16000,
            11 => 24000, // �O�{��
            12 => 24000,
            13 => 32000, // ��
        ];

        return $baseScore[$han] ?? 32000;
    }
}