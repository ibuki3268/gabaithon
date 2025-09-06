<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Tile; // Tileモデルをインポート
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * 指定された牌に関連するクイズの一覧を表示する（既存の機能）
     */
    public function show(Tile $tile)
    {
        // 牌（$tile）に紐づく問題を取得
        $questions = $tile->questions()->get();

        // 牌（tile）と問題リスト（questions）をビューに渡す
        // 注意: 'quiz.show' というビューファイルが `resources/views/quiz/` に必要です
        return view('quiz.show', [
            'tile' => $tile,
            'questions' => $questions,
        ]);
    }

    /**
     * クイズを開始し、最初の問題を表示する
     */
    public function start(Request $request)
    {
        // ログインユーザーの進捗データを取得、なければデフォルト値で新規作成
        $progress = Progress::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'day' => 1,
                'course_id' => 1,
                'difficulty_id' => 1,
                'progresses' => [],         // ← 'progress' から 'progresses' に変更
                'yaku_id' => 1,            // yaku_idを追加
                'status' => 'started',     // ← statusも追加
            ]
        );

        // 進捗に合致する問題を取得
        $questions = Question::where('day', $progress->day)
                             ->where('course_id', $progress->course_id)
                             ->where('difficulty_id', $progress->difficulty_id)
                             ->inRandomOrder() // 問題をランダムな順序で取得
                             ->get()
                             ->toArray();

        // 管理画面で1レコードに複数問題を JSON 文字列として保持している場合の対応
        // 例: $questions[0]['question'] が '{"questions": [...]}'' のような JSON 文字列
        if (count($questions) === 1 && isset($questions[0]['question']) && is_string($questions[0]['question'])) {
            $maybe = json_decode($questions[0]['question'], true);
            if (is_array($maybe) && isset($maybe['questions']) && is_array($maybe['questions'])) {
                $questions = $maybe['questions'];
            }
        }

        // もし問題が1問もなければ、専用の画面を表示
        if (empty($questions)) {
            return view('quiz.no_questions');
        }

        // 選択肢の配列をシャッフル (optionsカラムはJSONキャストされている想定)
        foreach ($questions as &$question) {
            if (isset($question['options']) && is_array($question['options'])) {
                shuffle($question['options']);
            }
        }

        // 必要な情報をセッションに保存
        $request->session()->put('questions', $questions);
        $request->session()->put('quiz_index', 0);
        $request->session()->put('score', []);

        // 最初の問題をビューに渡す
        return view('quiz.index', ['question' => $questions[0]]);
    }

    /**
     * ユーザーの回答を処理し、次の問題または結果画面へ遷移する
     */
    public function answer(Request $request)
    {
        $questions = $request->session()->get('questions');
        $index = $request->session()->get('quiz_index', 0);
        $score = $request->session()->get('score', []);

        if (!$questions) {
            return redirect()->route('quiz.start');
        }

        $selectedAnswer = $request->input('selected');
        $correctAnswer = $questions[$index]['answer'];
        $score[] = ($selectedAnswer === $correctAnswer);

        $index++;
        $request->session()->put('quiz_index', $index);
        $request->session()->put('score', $score);

        if ($index >= count($questions)) {
            return redirect()->route('quiz.result');
        }

        return view('quiz.index', ['question' => $questions[$index]]);
    }

    /**
     * クイズの結果を表示する
     */
    public function result(Request $request)
    {
        $score = $request->session()->get('score', []);
        $questions = $request->session()->get('questions', []);

        if (empty($score) || empty($questions)) {
            return redirect()->route('quiz.start')->with('error', 'クイズセッションが見つかりませんでした。');
        }
                
        $total = count($score);
        $correctCount = count(array_filter($score));

        return view('quiz.result', compact('score', 'questions', 'total', 'correctCount'));
    }
}