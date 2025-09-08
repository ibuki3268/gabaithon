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
     * 指定された牌に関連するクイズの一覧を表示
     */
    public function show(Tile $tile)
    {
        $questions = $tile->questions()->get();

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
        $courseId = $course ?? 1;
        $difficultyId = $difficulty ?? 1;
        $yakuId = $yaku ?? 1;

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

        $questionRecords = Question::where('day', $progress->day)
            ->where('course_id', $courseId)
            ->where('difficulty_id', $difficultyId)
            ->inRandomOrder()
            ->get()
            ->toArray();

        $questions = [];
        foreach ($questionRecords as $record) {
            if (isset($record['question']) && is_array($record['question'])) {
                if (isset($record['question']['questions']) && is_array($record['question']['questions'])) {
                    foreach ($record['question']['questions'] as $q) {
                        $questions[] = [
                            'text' => $q['question'] ?? '問題文なし',
                            'options' => $q['options'] ?? [],
                            'answer' => $q['answer'] ?? '',
                            'id' => $record['id'] ?? null,
                            'tile_id' => $record['id'] ?? null
                        ];
                    }
                } else {
                    $questions[] = [
                        'text' => $record['question']['question'] ?? $record['question']['text'] ?? '問題文なし',
                        'options' => $record['question']['options'] ?? [],
                        'answer' => $record['question']['answer'] ?? '',
                        'id' => $record['id'] ?? null,
                        'tile_id' => $record['id'] ?? null
                    ];
                }
            } elseif (isset($record['question']) && is_string($record['question'])) {
                $decoded = json_decode($record['question'], true);
                if ($decoded && isset($decoded['questions'])) {
                    foreach ($decoded['questions'] as $q) {
                        $questions[] = [
                            'text' => $q['question'] ?? '問題文なし',
                            'options' => $q['options'] ?? [],
                            'answer' => $q['answer'] ?? '',
                            'id' => $record['id'] ?? null,
                            'tile_id' => $record['id'] ?? null
                        ];
                    }
                }
            }
        }

        if (empty($questions)) {
            return view('quiz.no_questions');
        }

        foreach ($questions as &$question) {
            if (is_array($question['options'])) {
                shuffle($question['options']);
            }
        }

        $request->session()->put('questions', $questions);
        $request->session()->put('quiz_index', 0);
        $request->session()->put('score', []);
        $request->session()->put('current_course_id', $courseId);
        $request->session()->put('current_difficulty_id', $difficultyId);
        $request->session()->put('current_yaku_id', $yakuId);

        return view('quiz.index', ['question' => $questions[0]]);
    }

    /**
     * ユーザーの回答を処理
     */
    public function answer(Request $request)
    {
        $questions = $request->session()->get('questions', []);
        $index = $request->session()->get('quiz_index', 0);
        $score = $request->session()->get('score', []);

        if (empty($questions) || !isset($questions[$index])) {
            return redirect()->route('quiz.result');
        }

        $selectedAnswer = $request->input('selected');
        $correctAnswer = $questions[$index]['answer'] ?? null;
        $score[] = ($selectedAnswer === $correctAnswer);

        $index++;
        $request->session()->put('quiz_index', $index);
        $request->session()->put('score', $score);

        if ($index >= count($questions) || count($questions) === 0) {
            return redirect()->route('quiz.result');
        }

        return view('quiz.index', ['question' => $questions[$index]]);
    }

    /**
     * クイズの結果を表示
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
        $percentage = $total > 0 ? ($correctCount / $total) * 100 : 0;
        $pass = $percentage >= 80;

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

        $this->updateProgresses($progress, $questions, $pass);

        $request->session()->forget(['questions', 'quiz_index', 'score', 'current_course_id', 'current_difficulty_id', 'current_yaku_id']);

        return view('quiz.result', compact('score', 'questions', 'total', 'correctCount', 'percentage', 'pass'));
    }

    /**
     * プログレスデータを更新
     */
    private function updateProgresses($progress, $questions, $pass)
    {
        $ps = $progress->progresses;
        if (empty($ps)) {
            $ps = [];
        }

        foreach ($questions as $question) {
            $tileId = $this->getTileId($question);
            if ($tileId === null) {
                Log::warning('No tile_id found for question', ['question' => $question]);
                continue;
            }

            if ($pass) {
                if (!isset($ps[$tileId]) || $ps[$tileId] === null) {
                    $ps[$tileId] = $progress->day;
                    Log::debug('Set day for passed tile', [
                        'user_id' => $progress->user_id,
                        'tile_id' => $tileId,
                        'day' => $progress->day
                    ]);
                }
            }
        }

           // 合格・不合格に関わらず newstudy を false にする
    $progress->newstudy = false;

    $progress->save();

    Log::debug('Updated progresses', [
        'user_id' => $progress->user_id,
        'pass' => $pass,
        'updated_progresses' => $ps,
        'newstudy' => $progress->newstudy
        ]);
    }

    /**
     * 問題データから tile_id を取得
     */
    private function getTileId($question)
    {
        if (is_array($question)) {
            if (array_key_exists('tile_id', $question)) return $question['tile_id'];
            if (array_key_exists('id', $question)) return $question['id'];
        } elseif (is_object($question)) {
            if (isset($question->tile_id)) return $question->tile_id;
            if (isset($question->id)) return $question->id;
        }
        return null;
    }
}
