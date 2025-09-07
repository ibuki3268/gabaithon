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
     * �w�肳�ꂽ�v�Ɋ֘A����N�C�Y�̈ꗗ��\������i�����̋@�\�j
     */
    public function show(Tile $tile)
    {
        // �v�i$tile�j�ɕR�Â������擾
        $questions = $tile->questions()->get();

        // �v�itile�j�Ɩ�胊�X�g�iquestions�j���r���[�ɓn��
        return view('quiz.show', [
            'tile' => $tile,
            'questions' => $questions,
        ]);
    }

    /**
     * �N�C�Y���J�n���A�ŏ��̖���\������
     */
    public function start(Request $request, $course = null, $difficulty = null, $yaku = null)
    {
        // ���[�g�p�����[�^����l���擾�A�Ȃ���΃f�t�H���g�l
        $courseId = $course ?? 1;
        $difficultyId = $difficulty ?? 1;
        $yakuId = $yaku ?? 1;

        // ���O�C�����[�U�[�̐i���f�[�^���擾�A�Ȃ���΃f�t�H���g�l�ŐV�K�쐬
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

        // �i���ɍ��v��������擾
        $questionRecords = Question::where('day', $progress->day)
                             ->where('course_id', $courseId)
                             ->where('difficulty_id', $difficultyId)
                             ->inRandomOrder()
                             ->get()
                             ->toArray();

        // ���f�[�^�𐳋K��
        $questions = [];
        foreach ($questionRecords as $record) {
            // record['question'] ���z��iJSON�L���X�g���ꂽ��ԁj�̏ꍇ
            if (isset($record['question']) && is_array($record['question'])) {
                if (isset($record['question']['questions']) && is_array($record['question']['questions'])) {
                    // ������肪�܂܂�Ă���ꍇ
                    foreach ($record['question']['questions'] as $q) {
                        $questions[] = [
                            'text' => $q['question'] ?? '��蕶�Ȃ�',
                            'options' => $q['options'] ?? [],
                            'answer' => $q['answer'] ?? '',
                            'id' => $record['id'] ?? null,
                            'tile_id' => $record['id'] ?? null // tile_id�Ƃ��Ė��ID���g�p
                        ];
                    }
                } else {
                    // �P����̏ꍇ
                    $questions[] = [
                        'text' => $record['question']['question'] ?? $record['question']['text'] ?? '��蕶�Ȃ�',
                        'options' => $record['question']['options'] ?? [],
                        'answer' => $record['question']['answer'] ?? '',
                        'id' => $record['id'] ?? null,
                        'tile_id' => $record['id'] ?? null // tile_id�Ƃ��Ė��ID���g�p
                    ];
                }
            }
            // record['question'] ��������iJSON������j�̏ꍇ
            elseif (isset($record['question']) && is_string($record['question'])) {
                $decoded = json_decode($record['question'], true);
                if ($decoded && isset($decoded['questions'])) {
                    foreach ($decoded['questions'] as $q) {
                        $questions[] = [
                            'text' => $q['question'] ?? '��蕶�Ȃ�',
                            'options' => $q['options'] ?? [],
                            'answer' => $q['answer'] ?? '',
                            'id' => $record['id'] ?? null,
                            'tile_id' => $record['id'] ?? null // tile_id�Ƃ��Ė��ID���g�p
                        ];
                    }
                }
            }
        }

        // ������肪1����Ȃ���΁A��p�̉�ʂ�\��
        if (empty($questions)) {
            return view('quiz.no_questions');
        }

        // �I�������V���b�t��
        foreach ($questions as &$question) {
            if (is_array($question['options'])) {
                shuffle($question['options']);
            }
        }

        // �K�v�ȏ����Z�b�V�����ɕۑ�
        $request->session()->put('questions', $questions);
        $request->session()->put('quiz_index', 0);
        $request->session()->put('score', []);
        $request->session()->put('current_course_id', $courseId);
        $request->session()->put('current_difficulty_id', $difficultyId);
        $request->session()->put('current_yaku_id', $yakuId);

        // �ŏ��̖����r���[�ɓn��
        return view('quiz.index', ['question' => $questions[0]]);
    }

    /**
     * ���[�U�[�̉񓚂��������A���̖��܂��͌��ʉ�ʂ֑J�ڂ���
     */
    public function answer(Request $request)
    {
        $questions = $request->session()->get('questions', []);
        $index = $request->session()->get('quiz_index', 0);
        $score = $request->session()->get('score', []);

        // ��胊�X�g����A�܂��̓C���f�b�N�X���͈͊O�Ȃ猋�ʉ�ʂ�
        if (empty($questions)) {
            return redirect()->route('quiz.result');
        }

        if (!isset($questions[$index])) {
            // ���炩�̗��R�Ō��݂̃C���f�b�N�X�����݂��Ȃ��i�폜���j
            return redirect()->route('quiz.result');
        }

        $selectedAnswer = $request->input('selected');
        $correctAnswer = $questions[$index]['answer'] ?? null;
        $score[] = ($selectedAnswer === $correctAnswer);

        $index++;
        $request->session()->put('quiz_index', $index);
        $request->session()->put('score', $score);

        // �c���肪������Ό��ʉ�ʂ�
        if ($index >= count($questions) || count($questions) === 0) {
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
        $courseId = $request->session()->get('current_course_id', 1);
        $difficultyId = $request->session()->get('current_difficulty_id', 1);
        $yakuId = $request->session()->get('current_yaku_id', 1);

        if (empty($score) || empty($questions)) {
            return redirect()->route('quiz.start')->with('error', '�N�C�Y�Z�b�V������������܂���ł����B');
        }
                
        $total = count($score);
        $correctCount = count(array_filter($score));

        // �f�o�b�O���O
        Log::debug('QuizController::result called', [
            'user_id' => Auth::id(),
            'total' => $total,
            'correctCount' => $correctCount,
            'course_id' => $courseId,
            'difficulty_id' => $difficultyId,
            'yaku_id' => $yakuId,
        ]);

        // ���i����i80%�ȏ�����i�Ƃ���j
        $percentage = $total > 0 ? ($correctCount / $total) * 100 : 0;
        $pass = $percentage >= 80;

        // ���O�C�����[�U�[�� Progress ���X�V����
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

        // progresses�̍X�V����
        $this->updateProgresses($progress, $questions, $pass);

        // �Z�b�V�������N���A
        $request->session()->forget(['questions', 'quiz_index', 'score', 'current_course_id', 'current_difficulty_id', 'current_yaku_id']);

        return view('quiz.result', compact('score', 'questions', 'total', 'correctCount', 'percentage', 'pass'));
    }

    /**
     * �v���O���X�f�[�^���X�V����
     */
    private function updateProgresses($progress, $questions, $pass)
{
    $ps = $progress->progresses;

    // progresses �� null �̏ꍇ�� newstudy �� false �ɂ��ĕۑ�
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

        // tile_id �̘g���������i���݂��Ȃ���΁j
        if (!isset($ps[$tileId]) || !is_array($ps[$tileId])) {
            $ps[$tileId] = ['passed' => false, 'day' => null, 'newstudy' => true];
        }

        // ���i���� day �� null �̏ꍇ�̂� day ���L�^
        if ($pass && ($ps[$tileId]['day'] === null)) {
            $ps[$tileId]['day'] = $progress->day;
            $ps[$tileId]['passed'] = true; // ���i���̂ݍX�V
            Log::debug('Set day for passed tile', [
                'user_id' => $progress->user_id,
                'tile_id' => $tileId,
                'day' => $progress->day
            ]);
        }
        // �s���i�̂Ƃ��͍X�V���Ȃ�
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
     * ���f�[�^����tile_id���擾����
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