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
                    <h3 class="text-2xl font-bold mb-6">
                        {{ $question['text'] ?? '問題文がありません。' }}
                    </h3>

                  

                    {{-- 回答を送信するフォーム --}}
                    <form action="{{ route('quiz.answer') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- 選択肢をループでボタンとして表示 --}}
                        @if(isset($question['options']) && is_array($question['options']) && count($question['options']) > 0)
                            @foreach ($question['options'] as $option)
                                <button type="submit" name="selected" value="{{ $option }}"
                                        class="block w-full text-left p-4 bg-gray-200 dark:bg-gray-700 hover:bg-blue-200 dark:hover:bg-blue-700 rounded-lg transition-colors duration-200">
                                    {{ $option }}
                                </button>
                            @endforeach
                        @else
                            <div class="p-4 bg-red-100 dark:bg-red-800 border border-red-400 rounded">
                                <p>選択肢がありません。</p>
                                <p class="text-sm text-gray-600 mt-2">
                                    データ確認: {{ json_encode(array_keys($question)) }}
                                </p>
                            </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>