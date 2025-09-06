<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            �N�C�Y
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- ��蕶 --}}
                    @php
                        // $question �̓R���g���[���Ŕz��Ƃ��ēn����Ă���z��
                        // �t�H�[���o�b�N: text -> question_text -> question -> content['text']
                        $qText = null;
                        $options = [];

                        if (is_array($question)) {
                            if (array_key_exists('text', $question)) {
                                $qText = $question['text'];
                            } elseif (array_key_exists('question_text', $question)) {
                                $qText = $question['question_text'];
                            } elseif (array_key_exists('question', $question)) {
                                $qText = $question['question'];
                            } elseif (array_key_exists('content', $question) && is_array($question['content']) && array_key_exists('text', $question['content'])) {
                                $qText = $question['content']['text'];
                            }

                            // �I�����̌��ioptions �J���� or content['options']�j
                            if (array_key_exists('options', $question)) {
                                $options = $question['options'];
                            } elseif (array_key_exists('content', $question) && is_array($question['content']) && array_key_exists('options', $question['content'])) {
                                $options = $question['content']['options'];
                            }
                            // options �� JSON �������J���}��؂蕶����̏ꍇ�ɔz��֕ϊ�
                            if (is_string($options)) {
                                $decoded = json_decode($options, true);
                                if (is_array($decoded)) {
                                    $options = $decoded;
                                } else {
                                    // �J���}��؂�Ŋi�[����Ă���P�[�X��z��
                                    $options = array_map('trim', explode(',', $options));
                                }
                            }
                        }
                    @endphp

                    <h3 class="text-2xl font-bold mb-6">{{ $qText ?? '��蕶������܂���B' }}</h3>

                    {{-- �f�o�b�O: ���I�u�W�F�N�g�̐��f�[�^��\���iAPP_DEBUG ���̂݁j --}}
                    @if(config('app.debug'))
                        <div class="mt-4 p-3 bg-gray-100 text-sm rounded">
                            <div class="font-semibold mb-2">Debug: question raw</div>
                            <pre class="whitespace-pre-wrap">{!! nl2br(e(json_encode($question, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))) !!}</pre>
                        </div>
                    @endif

                    {{-- �񓚂𑗐M����t�H�[�� --}}
                    <form action="{{ route('quiz.answer') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- �I���������[�v�Ń{�^���Ƃ��ĕ\�� --}}
                        @if(is_array($options) && count($options) > 0)
                            @foreach ($options as $option)
                                <button type="submit" name="selected" value="{{ $option }}"
                                        class="block w-full text-left p-4 bg-gray-200 dark:bg-gray-700 hover:bg-blue-200 dark:hover:bg-blue-700 rounded-lg transition-colors duration-200">
                                    {{ $option }}
                                </button>
                            @endforeach
                        @else
                             <p>�I����������܂���B</p>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
