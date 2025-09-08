<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Course;
use App\Models\Difficulty;
use App\Models\Yaku;
use App\Models\Tile;

class HomeController extends Controller
{
    //最初に表示するとき用
    public function firstshow(Request $request)
    {
        $user = auth()->user();//このユーザーのレコード取得
        $hasProgress = session()->get('hasProgress',null);
        if(is_null($hasProgress)){
            $hasProgress = Progress::where('user_id', $user->id)->exists();
            session(['hasProgress' => $hasProgress]);
        }

        if ($request->wantsJson()) {//ボタンで戻ってきた場合
            session(['selectedTag' => null]);//戻るから新規か既存選んだのをリセットしとく

            return response()->json([
                'hasProgress' => $hasProgress,
            ]);
        }
        //＄Coursesにはコーステーブルのレコード一個一個がオブジェクトとして入っている。
        return view('dashboard', [
            'hasProgress' => $hasProgress,
        ]);
    }


    //新規か既存かを選択したとき用（new,exist的なのが来る）
    public function selectcourse(Request $request)
    {
        //難易度選択から戻ってきた場合
        $selectedTag = $request->input('selectedTag'); 
        session(['selectedTag' => $selectedTag]);//セッションに新規か既存か保存
        $selectedCourseId = session()->get('selectedCourseId',null);
        if($selectedCourseId !== null){
            session(['selectedCourseId' => null]);
        }
        
        $user = auth()->user();//このユーザーのレコード取得
        $hasProgress = session()->get('hasProgress',null);//コース全課程修了した場合このセッションももっかい判定しなおす必要あり
        if(is_null($hasProgress)){
            $hasProgress = Progress::where('user_id', $user->id)->exists();
            session(['hasProgress' => $hasProgress]);
        }
        $Courses = [];

        if ($hasProgress) {
            $Courses = Course::whereIn('id', function($query) use ($user) {//コーステーブルからプログレスにあるコースの情報を取ってきてるない場合は全部
                $query->select('course_id')
                      ->from('progress')
                      ->where('user_id', $user->id);
            })->where('is_public', 1)->get();
        } else {
            $Courses = Course::where('is_public', 1)->get();
        }

        //＄Coursesにはコーステーブルのレコード一個一個がオブジェクトとして入っている。
        return response()->json([
            'Courses' => $Courses,
            'selectedTag' => $selectedTag,
        ]);
    }

    //難易度選択画面用(コースIDが渡される)
    public function selectdifficulty(Request $request)
    {
        //戻ってきたとき用
        $DifficultyId = session()->get('DifficultyId', null);
        if ($DifficultyId !== null) {
            session(['DifficultyId' => null]);
        }


        $user = auth()->user();//このユーザーのレコード取得        
        $hasProgress = session()->get('hasProgress',null);//コース全課程修了した場合このセッションももっかい判定しなおす必要あり
        if(is_null($hasProgress)){
            $hasProgress = Progress::where('user_id', $user->id)->exists();
            session(['hasProgress' => $hasProgress]);
        }
        $selectedCourseId = $request->input('selectedCourseId'); //受け取ったコースID
        session(['selectedCourseId' => $selectedCourseId]);//セッションにコースID保存

        $selectedTag = session()->get('selectedTag',null);

        $Difficulties = [];

        if ($hasProgress) {
            if($selectedTag == 'existing'){
                $Difficulties = Difficulty::where('course_id', $selectedCourseId)
                        ->whereIn('id', function($query) use ($user, $selectedCourseId) {
                        $query->select('difficulty_id')
                            ->from('progress')
                            ->where('user_id', $user->id)
                            ->where('course_id', $selectedCourseId);
                })
                ->get();
            }elseif($selectedTag == 'new'){
                $Difficulties = Difficulty::where('course_id', $selectedCourseId)
                        ->whereNotIn('id', function($query) use ($user, $selectedCourseId) {
                        $query->select('difficulty_id')
                            ->from('progress')
                            ->where('user_id', $user->id)
                            ->where('course_id', $selectedCourseId);
                })
                ->get();
            }
        } else {
            $Difficulties = Difficulty::where('course_id', $selectedCourseId)->get();
        }

        $courseName = Course::where('id', $selectedCourseId)->value('name');
        session(['courseName' => $courseName]);

        //＄Coursesにはコーステーブルのレコード一個一個がオブジェクトとして入っている。
        return response()->json([
            'Difficulties' => $Difficulties,
            'selectedCourseId'=> $selectedCourseId,
            'courseName' => $courseName,
            'selectedTag' => $selectedTag
        ]);
    }

//役選択画面用(難易度IDが渡される)
    public function selectyaku(Request $request)
    {
        //戻ってきたとき用
        $YakuId = session()->get('YakuId', null);
        if ($YakuId !== null) {
            session(['YakuId' => null]);
        }

        $user = auth()->user();//このユーザーのレコード取得        
        $hasProgress = session()->get('hasProgress',null);//コース全課程修了した場合このセッションももっかい判定しなおす必要あり
        if(is_null($hasProgress)){
            $hasProgress = Progress::where('user_id', $user->id)->exists();
            session(['hasProgress' => $hasProgress]);
        }
        $selectedCourseId = session()->get('selectedCourseId',null);
        $selectedDifficultyId = $request->input('selectdifficultyId'); //難易度ID
        session(['selectdifficultyId' => $selectedDifficultyId]);//セッションに難易度ID保存

        $selectedTag = session()->get('selectedTag',null);//新規かそうじゃないか

        $Yakus = [];

        $Yakus = Difficulty::where('id', $selectedDifficultyId)->value('choice');
        if (is_string($Yakus)) {
            $Yakus = json_decode($Yakus, true); // 連想配列として取得
        }
        if (!$Yakus) {
            $Yakus = [];
        }

        $yakuIds = $Yakus['choices'] ?? [];

        $Yakusdata = Yaku::whereIn('id', $yakuIds)->get(); // ->get() を追加

        
        $difficultyName = Difficulty::where('id', $selectedDifficultyId)->value('name');
        session(['difficultyName' => $difficultyName]);
        $courseName = Course::where('id', $selectedCourseId)->value('name');
        
        //＄Coursesにはコーステーブルのレコード一個一個がオブジェクトとして入っている。
        return response()->json([
            'selectedTag' => $selectedTag,
            'selectedDifficultyId' => $selectedDifficultyId,
            'selectedCourseId'=> $selectedCourseId,
            'difficultyName' => $difficultyName,
            'courseName' => $courseName,
            'Yakusdata' => $Yakusdata // キー名を統一
        ]);
    }



    //牌選択画面用（yaku.idが渡される）
//牌選択画面用（yaku.idが渡される）
    public function selecthai(Request $request)
    {
        try {
            $user = auth()->user();
            $hasProgress = session()->get('hasProgress', null);
            if (is_null($hasProgress)) {
                $hasProgress = Progress::where('user_id', $user->id)->exists();
                session(['hasProgress' => $hasProgress]);
            }
            
            $selectedCourseId = session()->get('selectedCourseId', null);
            $selectedDifficultyId = session()->get('selectdifficultyId', null);
            $selectedTag = session()->get('selectedTag', null);
            $selectedYakuId = $request->input('selectyakuId');

            // デバッグ用ログ
            \Log::info('selecthai called', [
                'selectedYakuId' => $selectedYakuId,
                'selectedCourseId' => $selectedCourseId,
                'selectedDifficultyId' => $selectedDifficultyId,
                'selectedTag' => $selectedTag
            ]);

            // 役のstructureを取得
            $yakuStructure = Yaku::where('id', $selectedYakuId)->value('structure');
            \Log::info('Yaku structure raw', ['structure' => $yakuStructure]);

            $structureData = [];
            if (is_string($yakuStructure)) {
                $structureData = json_decode($yakuStructure, true);
            } else if (is_array($yakuStructure)) {
                $structureData = $yakuStructure;
            }

            if (!$structureData) {
                $structureData = [];
            }

            // structureからtile IDsを取得（構造に応じて調整）
            $tileIds = [];
            if (isset($structureData['structure'])) {
                $tileIds = $structureData['structure'];
            } else if (isset($structureData['tiles'])) {
                $tileIds = $structureData['tiles'];
            } else if (is_array($structureData)) {
                // 直接配列の場合
                $tileIds = $structureData;
            }

            \Log::info('Tile IDs extracted', ['tileIds' => $tileIds]);

            // Tilesデータを取得
// Tilesデータを取得（重複OK版）
            $Haisdata = [];
            if (!empty($tileIds)) {
                foreach ($tileIds as $id) {
                    $tile = Tile::find($id); // 個別に取得
                    if ($tile) {
                        $Haisdata[] = $tile; // そのまま追加（重複保持）
                    }
                }
            }


            // 役の名前を取得
            $yakuName = Yaku::where('id', $selectedYakuId)->value('name');
            $difficultyName = session()->get('difficultyName');
            $courseName = session()->get('courseName');
            
            session(['selectedYakuId' => $selectedYakuId]);
            
            return response()->json([
                'selectedTag' => $selectedTag,
                'selectedDifficultyId' => $selectedDifficultyId,
                'selectedCourseId' => $selectedCourseId,
                'selectedYakuId' => $selectedYakuId,
                'difficultyName' => $difficultyName,
                'courseName' => $courseName,
                'yakuName' => $yakuName,
                'Haisdata' => $Haisdata
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in selecthai', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}