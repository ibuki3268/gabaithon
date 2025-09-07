<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tile;

class TileSeeder extends Seeder
{
    public function run(): void
    {
        // 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // テーブルを空にする
        DB::table('tiles')->truncate();

        // 外部キー制約を有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tiles = [
            ['type' => 'm', 'num' => 1 , 'name' => '一萬', 'image_path' => 'man1.png'],
            ['type' => 'm', 'num' => 2 , 'name' => '二萬', 'image_path' => 'man2.png'],
            ['type' => 'm', 'num' => 3 , 'name' => '三萬', 'image_path' => 'man3.png'],
            ['type' => 'm', 'num' => 4 , 'name' => '四萬', 'image_path' => 'man4.png'],
            ['type' => 'm', 'num' => 5 , 'name' => '五萬', 'image_path' => 'man5.png'],
            ['type' => 'm', 'num' => 6 , 'name' => '六萬', 'image_path' => 'man6.png'],
            ['type' => 'm', 'num' => 7 , 'name' => '七萬', 'image_path' => 'man7.png'],
            ['type' => 'm', 'num' => 8 , 'name' => '八萬', 'image_path' => 'man8.png'],
            ['type' => 'm', 'num' => 9 , 'name' => '九萬', 'image_path' => 'man9.png'],

            ['type' => 's', 'num' => 1 , 'name' => '一索', 'image_path' => 'so1.png'],
            ['type' => 's', 'num' => 2 , 'name' => '二索', 'image_path' => 'so2.png'],
            ['type' => 's', 'num' => 3 , 'name' => '三索', 'image_path' => 'so3.png'],
            ['type' => 's', 'num' => 4 , 'name' => '四索', 'image_path' => 'so4.png'],
            ['type' => 's', 'num' => 5 , 'name' => '五索', 'image_path' => 'so5.png'],
            ['type' => 's', 'num' => 6 , 'name' => '六索', 'image_path' => 'so6.png'],
            ['type' => 's', 'num' => 7 , 'name' => '七索', 'image_path' => 'so7.png'],
            ['type' => 's', 'num' => 8 , 'name' => '八索', 'image_path' => 'so8.png'],
            ['type' => 's', 'num' => 9 , 'name' => '九索', 'image_path' => 'so9.png'],

            ['type' => 'p', 'num' => 1 , 'name' => '一筒', 'image_path' => 'pin1.png'],
            ['type' => 'p', 'num' => 2 , 'name' => '二筒', 'image_path' => 'pin2.png'],
            ['type' => 'p', 'num' => 3 , 'name' => '三筒', 'image_path' => 'pin3.png'],
            ['type' => 'p', 'num' => 4 , 'name' => '四筒', 'image_path' => 'pin4.png'],
            ['type' => 'p', 'num' => 5 , 'name' => '五筒', 'image_path' => 'pin5.png'],
            ['type' => 'p', 'num' => 6 , 'name' => '六筒', 'image_path' => 'pin6.png'],
            ['type' => 'p', 'num' => 7 , 'name' => '七筒', 'image_path' => 'pin7.png'],
            ['type' => 'p', 'num' => 8 , 'name' => '八筒', 'image_path' => 'pin8.png'],
            ['type' => 'p', 'num' => 9 , 'name' => '九筒', 'image_path' => 'pin9.png'],

            ['type' => 'j', 'num' => null , 'name' => '東', 'image_path' => 'z1ton.png'],
            ['type' => 'j', 'num' => null , 'name' => '南', 'image_path' => 'z2nan.png'],
            ['type' => 'j', 'num' => null , 'name' => '西', 'image_path' => 'z3sya.png'],
            ['type' => 'j', 'num' => null , 'name' => '北', 'image_path' => 'z4pe.png'],
            ['type' => 'j', 'num' => null , 'name' => '白', 'image_path' => 'z5haku.png'],
            ['type' => 'j', 'num' => null , 'name' => '發', 'image_path' => 'z6hatu.png'],
            ['type' => 'j', 'num' => null , 'name' => '中', 'image_path' => 'z7tyun.png'],

            ['type' => 'a', 'num' => 5 , 'name' => '赤五筒', 'image_path' => 'aka1pin.png'],
            ['type' => 'a', 'num' => 5 , 'name' => '赤五索', 'image_path' => 'aka2so.png'],
            ['type' => 'a', 'num' => 5 , 'name' => '赤五萬', 'image_path' => 'aka3man.png'],
        ];

        // DBに挿入
        foreach ($tiles as $tile) {
            Tile::create($tile);
        }
    }
}
