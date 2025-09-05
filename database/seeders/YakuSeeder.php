<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Yaku;

class YakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Yaku::truncate(); // テーブルを一旦空にする
        $path = database_path('seeders/json/yakus'); // yakusまでのパス

        //役の名前、翻数（仮）、構造を入れたjsonファイル名
        $yakus = [
            ['name' => '国士無双', 'level' => 4, 'json_file' => 'kokusi.json'],
            ['name' => '緑一色', 'level' => 4, 'json_file' => 'ryu-i-so.json'],
            //ここにほかの役を追加してほしいっす！！！
        ]




        foreach ($yakus as $yaku) {
            $structure = File::get($path . '/' . $yaku['json_file']);//ファイルまでのパス生成
            
            Yaku::create([
                'name' => $yaku['name'],
                'level' => $yaku['level'],
                'structure' => $structure,
            ]);
        }
    }
}
