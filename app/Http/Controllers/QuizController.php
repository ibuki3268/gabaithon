<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Tile; // Tileモデルをインポート
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
                'progresses' => [],
                'yaku_id' => 1,
                'status' => 'started',
            ]
        );

        // 進捗に合致する問題を取得
        $questionRecords = Question::where('day', $progress->day)
                             ->where('course_id', $progress->course_id)
                             ->where('difficulty_id', $progress->difficulty_id)
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
                            'id' => $record['id'] ?? null
                        ];
                    }
                } else {
                    // 単一問題の場合
                    $questions[] = [
                        'text' => $record['question']['question'] ?? $record['question']['text'] ?? '問題文なし',
                        'options' => $record['question']['options'] ?? [],
                        'answer' => $record['question']['answer'] ?? '',
                        'id' => $record['id'] ?? null
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
                            'id' => $record['id'] ?? null
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

        if (empty($score) || empty($questions)) {
            return redirect()->route('quiz.start')->with('error', 'クイズセッションが見つかりませんでした。');
        }
                
        $total = count($score);
        $correctCount = count(array_filter($score));

        // デバッグ: result が呼ばれたかとセッションデータの確認
        Log::debug('QuizController::result called', [
            'user_id' => Auth::id(),
            'total' => $total,
            'correctCount' => $correctCount,
            'score_sample' => array_slice($score, 0, 10),
            'questions_count' => is_array($questions) ? count($questions) : (is_object($questions) ? count((array)$questions) : 0),
        ]);

    // 合格判定（80%以上を合格とする）
    $percentage = $total > 0 ? ($correctCount / $total) * 100 : 0;
    $pass = $percentage >= 80;

    // ログインユーザーの Progress を更新する
    $progress = Progress::firstOrCreate(
        ['user_id' => Auth::id()],
        [
            'day' => 1,
            'course_id' => 1,
            'difficulty_id' => 1,
            'progresses' => [],
            'yaku_id' => 1,
            'status' => 'started',
        ]
    );

    // progresses が null の場合は newstudy を false にして保存
    $ps = $progress->progresses;
    if (is_null($ps)) {
        $progress->newstudy = false;
        $progress->save();
    } else {
        // 各 question に tile_id がある前提で処理
        foreach ($questions as $i => $q) {
            // tile_id が無ければ question の id を使う
            $tileId = null;
            if (is_array($q)) {
                if (array_key_exists('tile_id', $q)) {
                    $tileId = $q['tile_id'];
                } elseif (array_key_exists('id', $q)) {
                    $tileId = $q['id'];
                }
            } elseif (is_object($q)) {
                if (isset($q->tile_id)) {
                    $tileId = $q->tile_id;
                } elseif (isset($q->id)) {
                    $tileId = $q->id;
                }
            }

            if ($tileId === null) {
                continue; // id も見つからなければスキップ
            }

            if (!isset($ps[$tileId]) || !is_array($ps[$tileId])) {
                $ps[$tileId] = ['passed' => false, 'day' => null, 'newstudy' => true];
            }

            // 要件: 合格かつ day が null の場合に day を挿入
            if ($pass && ($ps[$tileId]['day'] === null)) {
                $ps[$tileId]['day'] = $progress->day;
            }

            // 合否は上書き
            $ps[$tileId]['passed'] = $pass;
            // newstudy は既存値を維持（要件での変更は progress.json が null の場合のみ）
        }

        $progress->progresses = $ps;
        $progress->save();
    }

    return view('quiz.result', compact('score', 'questions', 'total', 'correctCount', 'percentage', 'pass'));
    }
}