<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            クイズ結果
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-3xl font-bold text-center mb-4">結果発表</h3>

                    <p class="text-xl text-center mb-8">
                        あなたのスコアは... <span class="text-4xl font-black text-blue-500">{{ $correctCount }} / {{ $total }}</span> 問正解です！
                    </p>

                    {{-- 各問題の結果を詳細表示 --}}
                    <div class="space-y-2 mb-8">
                        @foreach($questions as $index => $question)
                            @php
                                $qText = null;
                                // 配列として渡されるケース
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
                                }

                                // オブジェクトとして渡されるケース
                                if ($qText === null && is_object($question)) {
                                    if (isset($question->text)) {
                                        $qText = $question->text;
                                    } elseif (isset($question->question_text)) {
                                        $qText = $question->question_text;
                                    } elseif (isset($question->question)) {
                                        $qText = $question->question;
                                    } elseif (isset($question->content) && is_array($question->content) && array_key_exists('text', $question->content)) {
                                        $qText = $question->content['text'];
                                    }
                                }

                                // 最終フォールバック
                                if ($qText === null) {
                                    if (is_string($question)) {
                                        $qText = $question;
                                    } else {
                                        $qText = json_encode($question, JSON_UNESCAPED_UNICODE);
                                    }
                                }
                            @endphp

                            <div class="p-3 rounded-lg {{ $score[$index] ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                <p class="font-bold">{{ $index + 1 }}. {{ $qText }}</p>
                                <p>正解: {{ is_array($question) && array_key_exists('answer', $question) ? $question['answer'] : (is_object($question) && isset($question->answer) ? $question->answer : '') }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- もう一度挑戦するボタン --}}
                    <div class="text-center">
                        <a href="{{ route('quiz.start') }}"
                           class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                            もう一度挑戦する
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
