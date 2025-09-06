<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Yaku;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class YakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
            Yaku::truncate();
        Schema::enableForeignKeyConstraints(); // テーブルを一旦空にする
        $path = database_path('seeders/json/yakus'); // yakusまでのパス

        //役の名前、翻数（仮）、構造を入れたjsonファイル名
        $yakus = [
            ['name' => '断么九', 'level' => 1, 'structure' => 'tanyao.json'],
            ['name' => '三色同順', 'level' => 2, 'structure' => 'sansyokudoujyun.json'],
            ['name' => '三色同刻', 'level' => 2, 'structure' => 'sansyokudoukou.json'],
            ['name' => '一気通貫', 'level' => 2, 'structure' => 'ikkituukan.json'],
            ['name' => '対々和', 'level' => 2, 'structure' => 'toitoihou.json'],
            ['name' => '三暗刻', 'level' => 2, 'structure' => 'sannannko.json'],
            ['name' => '全帯么', 'level' => 2, 'structure' => 'tyanta.json'],
            ['name' => '混老頭', 'level' => 2, 'structure' => 'honnro-to.json'],
            ['name' => '小三元', 'level' => 2, 'structure' => 'syousangen.json'],
            ['name' => '七対子', 'level' => 2, 'structure' => 'ti-toitu.json'],
            ['name' => '二盃口', 'level' => 3, 'structure' => 'ryanpe-kou.json'],
            ['name' => '混一色', 'level' => 3, 'structure' => 'honitu.json'],
            ['name' => '純全帯么', 'level' => 3, 'structure' => 'jyuntyanta.json'],
            ['name' => '清一色', 'level' => 4, 'structure' => 'tinitu.json'],
            ['name' => '四暗刻', 'level' => 5, 'structure' => 'su-anko.json'],
            ['name' => '大三元', 'level' => 5, 'structure' => 'daisangen.json'],
            ['name' => '国士無双', 'level' => 5, 'structure' => 'kokusimusou.json'],
            ['name' => '四喜和', 'level' => 5, 'structure' => 'su-si-ho-.json'],
            ['name' => '字一色', 'level' => 5, 'structure' => 'tu-i-so-.json'],
            ['name' => '九連宝燈', 'level' => 5, 'structure' => 'tyu-renpo-to-.json'],
            ['name' => '緑一色', 'level' => 5, 'structure' => 'ryu-i-so.json'],
            ['name' => '清老頭', 'level' => 5, 'structure' => 'tinro-to-.json'],
        ];




        foreach ($yakus as $yaku) {
            $structure = File::get($path . '/' . $yaku['structure']);//ファイルまでのパス生成
            
            Yaku::create([
                'name' => $yaku['name'],
                'level' => $yaku['level'],
                'structure' => $structure,
            ]);
        }
    }
}
