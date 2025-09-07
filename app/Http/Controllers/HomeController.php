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


        //＄Coursesにはコーステーブルのレコード一個一個がオブジェクトとして入っている。
        return response()->json([
            'Difficulties' => $Difficulties,
            'selectedCourseId'=> $selectedCourseId,
            'courseName' => $courseName,
            'selectedTag' => $selectedTag
        ]);
    }
}