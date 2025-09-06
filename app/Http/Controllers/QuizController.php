<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Tile; // Tile���f�����C���|�[�g
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * �w�肳�ꂽ�v�Ɋ֘A����N�C�Y�̈ꗗ��\������i�����̋@�\�j
     */
    public function show(Tile $tile)
    {
        // �v�i$tile�j�ɕR�Â������擾
        $questions = $tile->questions()->get();

        // �v�itile�j�Ɩ�胊�X�g�iquestions�j���r���[�ɓn��
        // ����: 'quiz.show' �Ƃ����r���[�t�@�C���� `resources/views/quiz/` �ɕK�v�ł�
        return view('quiz.show', [
            'tile' => $tile,
            'questions' => $questions,
        ]);
    }

    /**
     * �N�C�Y���J�n���A�ŏ��̖���\������
     */
    public function start(Request $request)
    {
        // ���O�C�����[�U�[�̐i���f�[�^���擾�A�Ȃ���΃f�t�H���g�l�ŐV�K�쐬
        $progress = Progress::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'day' => 1,
                'course_id' => 1,
                'difficulty_id' => 1,
                'progresses' => [],         // �� 'progress' ���� 'progresses' �ɕύX
                'yaku_id' => 1,            // yaku_id��ǉ�
                'status' => 'started',     // �� status���ǉ�
            ]
        );

        // �i���ɍ��v��������擾
        $questions = Question::where('day', $progress->day)
                             ->where('course_id', $progress->course_id)
                             ->where('difficulty_id', $progress->difficulty_id)
                             ->inRandomOrder() // ���������_���ȏ����Ŏ擾
                             ->get()
                             ->toArray();

        // �Ǘ���ʂ�1���R�[�h�ɕ������� JSON ������Ƃ��ĕێ����Ă���ꍇ�̑Ή�
        // ��: $questions[0]['question'] �� '{"questions": [...]}'' �̂悤�� JSON ������
        if (count($questions) === 1 && isset($questions[0]['question']) && is_string($questions[0]['question'])) {
            $maybe = json_decode($questions[0]['question'], true);
            if (is_array($maybe) && isset($maybe['questions']) && is_array($maybe['questions'])) {
                $questions = $maybe['questions'];
            }
        }

        // ������肪1����Ȃ���΁A��p�̉�ʂ�\��
        if (empty($questions)) {
            return view('quiz.no_questions');
        }

        // �I�����̔z����V���b�t�� (options�J������JSON�L���X�g����Ă���z��)
        foreach ($questions as &$question) {
            if (isset($question['options']) && is_array($question['options'])) {
                shuffle($question['options']);
            }
        }

        // �K�v�ȏ����Z�b�V�����ɕۑ�
        $request->session()->put('questions', $questions);
        $request->session()->put('quiz_index', 0);
        $request->session()->put('score', []);

        // �ŏ��̖����r���[�ɓn��
        return view('quiz.index', ['question' => $questions[0]]);
    }

    /**
     * ���[�U�[�̉񓚂��������A���̖��܂��͌��ʉ�ʂ֑J�ڂ���
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
     * �N�C�Y�̌��ʂ�\������
     */
    public function result(Request $request)
    {
        $score = $request->session()->get('score', []);
        $questions = $request->session()->get('questions', []);

        if (empty($score) || empty($questions)) {
            return redirect()->route('quiz.start')->with('error', '�N�C�Y�Z�b�V������������܂���ł����B');
        }
                
        $total = count($score);
        $correctCount = count(array_filter($score));

        return view('quiz.result', compact('score', 'questions', 'total', 'correctCount'));
    }
}