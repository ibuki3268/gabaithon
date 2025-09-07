<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Tile; // Tile���f�����C���|�[�g
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
                'progresses' => [],
                'yaku_id' => 1,
                'status' => 'started',
            ]
        );

        // �i���ɍ��v��������擾
        $questionRecords = Question::where('day', $progress->day)
                             ->where('course_id', $progress->course_id)
                             ->where('difficulty_id', $progress->difficulty_id)
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
                            'id' => $record['id'] ?? null
                        ];
                    }
                } else {
                    // �P����̏ꍇ
                    $questions[] = [
                        'text' => $record['question']['question'] ?? $record['question']['text'] ?? '��蕶�Ȃ�',
                        'options' => $record['question']['options'] ?? [],
                        'answer' => $record['question']['answer'] ?? '',
                        'id' => $record['id'] ?? null
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
                            'id' => $record['id'] ?? null
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

        if (empty($score) || empty($questions)) {
            return redirect()->route('quiz.start')->with('error', '�N�C�Y�Z�b�V������������܂���ł����B');
        }
                
        $total = count($score);
        $correctCount = count(array_filter($score));

        // �f�o�b�O: result ���Ă΂ꂽ���ƃZ�b�V�����f�[�^�̊m�F
        Log::debug('QuizController::result called', [
            'user_id' => Auth::id(),
            'total' => $total,
            'correctCount' => $correctCount,
            'score_sample' => array_slice($score, 0, 10),
            'questions_count' => is_array($questions) ? count($questions) : (is_object($questions) ? count((array)$questions) : 0),
        ]);

    // ���i����i80%�ȏ�����i�Ƃ���j
    $percentage = $total > 0 ? ($correctCount / $total) * 100 : 0;
    $pass = $percentage >= 80;

    // ���O�C�����[�U�[�� Progress ���X�V����
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

    // progresses �� null �̏ꍇ�� newstudy �� false �ɂ��ĕۑ�
    $ps = $progress->progresses;
    if (is_null($ps)) {
        $progress->newstudy = false;
        $progress->save();
    } else {
        // �e question �� tile_id ������O��ŏ���
        foreach ($questions as $i => $q) {
            // tile_id ��������� question �� id ���g��
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
                continue; // id ��������Ȃ���΃X�L�b�v
            }

            if (!isset($ps[$tileId]) || !is_array($ps[$tileId])) {
                $ps[$tileId] = ['passed' => false, 'day' => null, 'newstudy' => true];
            }

            // �v��: ���i���� day �� null �̏ꍇ�� day ��}��
            if ($pass && ($ps[$tileId]['day'] === null)) {
                $ps[$tileId]['day'] = $progress->day;
            }

            // ���ۂ͏㏑��
            $ps[$tileId]['passed'] = $pass;
            // newstudy �͊����l���ێ��i�v���ł̕ύX�� progress.json �� null �̏ꍇ�̂݁j
        }

        $progress->progresses = $ps;
        $progress->save();
    }

    return view('quiz.result', compact('score', 'questions', 'total', 'correctCount', 'percentage', 'pass'));
    }
}