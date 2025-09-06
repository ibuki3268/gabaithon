<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            クイズ
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- 問題文 --}}
                    @php
                        // $question はコントローラで配列として渡されている想定
                        // フォールバック: text -> question_text -> question -> content['text']
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

                            // 選択肢の候補（options カラム or content['options']）
                            if (array_key_exists('options', $question)) {
                                $options = $question['options'];
                            } elseif (array_key_exists('content', $question) && is_array($question['content']) && array_key_exists('options', $question['content'])) {
                                $options = $question['content']['options'];
                            }
                            // options が JSON 文字列やカンマ区切り文字列の場合に配列へ変換
                            if (is_string($options)) {
                                $decoded = json_decode($options, true);
                                if (is_array($decoded)) {
                                    $options = $decoded;
                                } else {
                                    // カンマ区切りで格納されているケースを想定
                                    $options = array_map('trim', explode(',', $options));
                                }
                            }
                        }
                    @endphp

                    <h3 class="text-2xl font-bold mb-6">{{ $qText ?? '問題文がありません。' }}</h3>

                    {{-- デバッグ表示は削除しました --}}

                    {{-- 回答を送信するフォーム --}}
                    <form action="{{ route('quiz.answer') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- 選択肢をループでボタンとして表示 --}}
                        @if(is_array($options) && count($options) > 0)
                            @foreach ($options as $option)
                                <button type="submit" name="selected" value="{{ $option }}"
                                        class="block w-full text-left p-4 bg-gray-200 dark:bg-gray-700 hover:bg-blue-200 dark:hover:bg-blue-700 rounded-lg transition-colors duration-200">
                                    {{ $option }}
                                </button>
                            @endforeach
                        @else
                             <p>選択肢がありません。</p>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
