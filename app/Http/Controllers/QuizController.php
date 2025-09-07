<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Tile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        return view('quiz.show', [
            'tile' => $tile,
            'questions' => $questions,
        ]);
    }

    /**
     * クイズを開始し、最初の問題を表示する
     */
    public function start(Request $request, $course = null, $difficulty = null, $yaku = null)
    {
        // ルートパラメータから値を取得、なければデフォルト値
        $courseId = $course ?? 1;
        $difficultyId = $difficulty ?? 1;
        $yakuId = $yaku ?? 1;

        // ログインユーザーの進捗データを取得、なければデフォルト値で新規作成
        $progress = Progress::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'day' => 1,
                'course_id' => $courseId,
                'difficulty_id' => $difficultyId,
                'progresses' => [],
                'yaku_id' => $yakuId,
                'status' => 'started',
            ]
        );

        // 進捗に合致する問題を取得
        $questionRecords = Question::where('day', $progress->day)
                             ->where('course_id', $courseId)
                             ->where('difficulty_id', $difficultyId)
                             ->inRandomOrder()
                             ->get()
                             ->toArray();

        // 問題データを正規化
        $questions = [];
        foreach ($questionRecords as $record) {
            // record['question'] が配列（JSONキャストされた状態）の場合
            if (isset($record['question']) && is_array($record['question'])) {
                if (isset($record['question']['questions']) && is_array($record['question']['questions'])) {
                    // 複数問題が含まれている場合
                    foreach ($record['question']['questions'] as $q) {
                        $questions[] = [
                            'text' => $q['question'] ?? '問題文なし',
                            'options' => $q['options'] ?? [],
                            'answer' => $q['answer'] ?? '',
                            'id' => $record['id'] ?? null,
                            'tile_id' => $record['id'] ?? null // tile_idとして問題IDを使用
                        ];
                    }
                } else {
                    // 単一問題の場合
                    $questions[] = [
                        'text' => $record['question']['question'] ?? $record['question']['text'] ?? '問題文なし',
                        'options' => $record['question']['options'] ?? [],
                        'answer' => $record['question']['answer'] ?? '',
                        'id' => $record['id'] ?? null,
                        'tile_id' => $record['id'] ?? null // tile_idとして問題IDを使用
                    ];
                }
            }
            // record['question'] が文字列（JSON文字列）の場合
            elseif (isset($record['question']) && is_string($record['question'])) {
                $decoded = json_decode($record['question'], true);
                if ($decoded && isset($decoded['questions'])) {
                    foreach ($decoded['questions'] as $q) {
                        $questions[] = [
                            'text' => $q['question'] ?? '問題文なし',
                            'options' => $q['options'] ?? [],
                            'answer' => $q['answer'] ?? '',
                            'id' => $record['id'] ?? null,
                            'tile_id' => $record['id'] ?? null // tile_idとして問題IDを使用
                        ];
                    }
                }
            }
        }

        // もし問題が1問もなければ、専用の画面を表示
        if (empty($questions)) {
            return view('quiz.no_questions');
        }

        // 選択肢をシャッフル
        foreach ($questions as &$question) {
            if (is_array($question['options'])) {
                shuffle($question['options']);
            }
        }

        // 必要な情報をセッションに保存
        $request->session()->put('questions', $questions);
        $request->session()->put('quiz_index', 0);
        $request->session()->put('score', []);
        $request->session()->put('current_course_id', $courseId);
        $request->session()->put('current_difficulty_id', $difficultyId);
        $request->session()->put('current_yaku_id', $yakuId);

        // 最初の問題をビューに渡す
        return view('quiz.index', ['question' => $questions[0]]);
    }

    /**
     * ユーザーの回答を処理し、次の問題または結果画面へ遷移する
     */
    public function answer(Request $request)
    {
        $questions = $request->session()->get('questions', []);
        $index = $request->session()->get('quiz_index', 0);
        $score = $request->session()->get('score', []);

        // 問題リストが空、またはインデックスが範囲外なら結果画面へ
        if (empty($questions)) {
            return redirect()->route('quiz.result');
        }

        if (!isset($questions[$index])) {
            // 何らかの理由で現在のインデックスが存在しない（削除等）
            return redirect()->route('quiz.result');
        }

        $selectedAnswer = $request->input('selected');
        $correctAnswer = $questions[$index]['answer'] ?? null;
        $score[] = ($selectedAnswer === $correctAnswer);

        $index++;
        $request->session()->put('quiz_index', $index);
        $request->session()->put('score', $score);

        // 残り問題が無ければ結果画面へ
        if ($index >= count($questions) || count($questions) === 0) {
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
        $courseId = $request->session()->get('current_course_id', 1);
        $difficultyId = $request->session()->get('current_difficulty_id', 1);
        $yakuId = $request->session()->get('current_yaku_id', 1);

        if (empty($score) || empty($questions)) {
            return redirect()->route('quiz.start')->with('error', 'クイズセッションが見つかりませんでした。');
        }
                
        $total = count($score);
        $correctCount = count(array_filter($score));

        // デバッグログ
        Log::debug('QuizController::result called', [
            'user_id' => Auth::id(),
            'total' => $total,
            'correctCount' => $correctCount,
            'course_id' => $courseId,
            'difficulty_id' => $difficultyId,
            'yaku_id' => $yakuId,
        ]);

        // 合格判定（80%以上を合格とする）
        $percentage = $total > 0 ? ($correctCount / $total) * 100 : 0;
        $pass = $percentage >= 80;

        // ログインユーザーの Progress を更新する
        $progress = Progress::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'day' => 1,
                'course_id' => $courseId,
                'difficulty_id' => $difficultyId,
                'progresses' => [],
                'newstudy' => true,
                'yaku_id' => $yakuId,
                'status' => 'started',
            ]
        );

        // progressesの更新処理
        $this->updateProgresses($progress, $questions, $pass);

        // セッションをクリア
        $request->session()->forget(['questions', 'quiz_index', 'score', 'current_course_id', 'current_difficulty_id', 'current_yaku_id']);

        return view('quiz.result', compact('score', 'questions', 'total', 'correctCount', 'percentage', 'pass'));
    }

    /**
     * プログレスデータを更新する
     */
    private function updateProgresses($progress, $questions, $pass)
{
    $ps = $progress->progresses;

    // progresses が null の場合は newstudy を false にして保存
    if (empty($ps)) { 
    $progress->newstudy = false;
    $progress->save();
    Log::debug('Progress was null or empty, set newstudy to false', ['user_id' => $progress->user_id]);
    return;
}
    foreach ($questions as $question) {
        $tileId = $this->getTileId($question);

        if ($tileId === null) {
            Log::warning('No tile_id found for question', ['question' => $question]);
            continue;
        }

        // tile_id の枠を初期化（存在しなければ）
        if (!isset($ps[$tileId]) || !is_array($ps[$tileId])) {
            $ps[$tileId] = ['passed' => false, 'day' => null, 'newstudy' => true];
        }

        // 合格かつ day が null の場合のみ day を記録
        if ($pass && ($ps[$tileId]['day'] === null)) {
            $ps[$tileId]['day'] = $progress->day;
            $ps[$tileId]['passed'] = true; // 合格時のみ更新
            Log::debug('Set day for passed tile', [
                'user_id' => $progress->user_id,
                'tile_id' => $tileId,
                'day' => $progress->day
            ]);
        }
        // 不合格のときは更新しない
    }

    $progress->progresses = $ps;
    $progress->save();

    Log::debug('Updated progresses', [
        'user_id' => $progress->user_id,
        'pass' => $pass,
        'updated_progresses' => $ps
    ]);
}

    /**
     * 問題データからtile_idを取得する
     */
    private function getTileId($question)
    {
        if (is_array($question)) {
            if (array_key_exists('tile_id', $question)) {
                return $question['tile_id'];
            } elseif (array_key_exists('id', $question)) {
                return $question['id'];
            }
        } elseif (is_object($question)) {
            if (isset($question->tile_id)) {
                return $question->tile_id;
            } elseif (isset($question->id)) {
                return $question->id;
            }
        }
        
        return null;
    }
}