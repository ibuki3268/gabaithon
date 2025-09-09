<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class QuizController extends Controller
{
    /**
     * クイズを開始する
     */
    public function start(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        // カテゴリー情報を取得
        $category = DB::table('categories')->where('id', $categoryId)->first();
        
        if (!$category) {
            return redirect()->route('dashboard')->with('error', 'カテゴリーが見つかりません。');
        }
        
        // このカテゴリーの問題をランダムに3つ取得
        $questions = DB::table('questions')
            ->where('category_id', $categoryId)
            ->inRandomOrder()
            ->limit(3)
            ->get();
        
        if ($questions->isEmpty()) {
            return view('quiz.no_questions', compact('category'));
        }
        
        // choicesテーブルから選択肢を取得
        $questionsWithOptions = [];
        foreach ($questions as $question) {
            // 1つの問題に対するすべての選択肢を取得
            $choices = DB::table('choices')
                ->where('question_id', $question->id)
                ->get();
            
            if ($choices->isNotEmpty()) {
                $options = [];
                $correctIndex = 0;
                
                foreach ($choices as $index => $choice) {
                    $options[] = $choice->text;
                    // is_correctが1の場合、そのインデックスを記録
                    if ($choice->is_correct == 1) {
                        $correctIndex = $index;
                    }
                }
                
                if (!empty($options)) {
                    $questionsWithOptions[] = [
                        'id' => $question->id,
                        'text' => $question->question,
                        'options' => $options,
                        'correct_index' => $correctIndex
                    ];
                }
            }
        }
        
        if (empty($questionsWithOptions)) {
            return view('quiz.no_questions', compact('category'));
        }
        
        // セッションにクイズデータを保存
        Session::put('quiz_data', [
            'category_id' => $categoryId,
            'questions' => $questionsWithOptions,
            'current_index' => 0,
            'score' => [],
            'answers' => []
        ]);
        
        return $this->showQuestion();
    }
    
    /**
     * 現在の問題を表示する
     */
    public function showQuestion()
    {
        $quizData = Session::get('quiz_data');
        
        if (!$quizData) {
            return redirect()->route('dashboard');
        }
        
        $currentIndex = $quizData['current_index'];
        $questions = $quizData['questions'];
        
        if ($currentIndex >= count($questions)) {
            return $this->showResult();
        }
        
        $category = DB::table('categories')->where('id', $quizData['category_id'])->first();
        $question = $questions[$currentIndex];
        
        return view('quiz.index', [
            'category' => $category,
            'question' => $question,
            'current_question' => $currentIndex + 1,
            'total_questions' => count($questions)
        ]);
    }
    
    /**
     * 回答を処理する
     */
    public function answer(Request $request)
    {
        $quizData = Session::get('quiz_data');
        
        if (!$quizData) {
            return redirect()->route('dashboard');
        }
        
        $selectedOption = $request->get('selected');
        $currentIndex = $quizData['current_index'];
        $currentQuestion = $quizData['questions'][$currentIndex];
        
        // 選択された回答のインデックスを取得
        $selectedIndex = null;
        foreach ($currentQuestion['options'] as $index => $option) {
            if ($option === $selectedOption) {
                $selectedIndex = $index;
                break;
            }
        }
        
        // 回答をセッションに保存
        $isCorrect = $selectedIndex !== null && $selectedIndex === $currentQuestion['correct_index'];
        $correctAnswer = $currentQuestion['options'][$currentQuestion['correct_index']] ?? '';
        
        $quizData['answers'][$currentIndex] = $selectedOption;
        $quizData['score'][$currentIndex] = [
            'selected' => $selectedOption,
            'correct' => $correctAnswer,
            'is_correct' => $isCorrect
        ];
        
        // 次の問題へ
        $quizData['current_index']++;
        
        Session::put('quiz_data', $quizData);
        
        // 全問題が終了した場合は結果画面へ
        if ($quizData['current_index'] >= count($quizData['questions'])) {
            return $this->showResult();
        }
        
        return $this->showQuestion();
    }
    
    /**
     * クイズ結果を表示する
     */
    public function showResult()
    {
        $quizData = Session::get('quiz_data');
        
        if (!$quizData) {
            return redirect()->route('dashboard');
        }
        
        $category = DB::table('categories')->where('id', $quizData['category_id'])->first();
        $score = $quizData['score'];
        $questions = $quizData['questions'];
        
        // スコア計算
        $correctCount = 0;
        foreach ($score as $result) {
            if ($result['is_correct']) {
                $correctCount++;
            }
        }
        
        $total = count($questions);
        $percentage = $total > 0 ? ($correctCount / $total) * 100 : 0;
        $pass = $percentage >= 70; // 70%以上で合格
        
        return view('quiz.result', [
            'category' => $category,
            'score' => $score,
            'questions' => $questions,
            'correctCount' => $correctCount,
            'total' => $total,
            'percentage' => $percentage,
            'pass' => $pass
        ]);
    }
    
    /**
     * クイズを再挑戦する
     */
    public function retry()
    {
        $quizData = Session::get('quiz_data');
        
        if (!$quizData) {
            return redirect()->route('dashboard');
        }
        
        // セッションをクリアして新しく開始
        Session::forget('quiz_data');
        
        // 同じカテゴリーで新しい問題を取得
        $request = new Request();
        $request->merge(['category_id' => $quizData['category_id']]);
        
        return $this->start($request);
    }
    
    /**
     * クイズセッションを終了してホームに戻る
     */
    public function finish()
    {
        Session::forget('quiz_data');
        return redirect()->route('dashboard');
    }
}