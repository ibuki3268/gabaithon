<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- 進捗表示 --}}
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>問題 {{ $current_question }} / {{ $total_questions }}</span>
                            <span>{{ round(($current_question / $total_questions) * 100) }}% 完了</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($current_question / $total_questions) * 100 }}%"></div>
                        </div>
                    </div>

                    {{-- 問題文 --}}
                    <h3 class="text-2xl font-bold mb-6">{{ $question['text'] }}</h3>

                    {{-- 回答フォーム --}}
                    <form action="{{ route('quiz.answer') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            {{-- 選択肢をループで表示 --}}
                            @foreach($question['options'] as $index => $option)
                                <div>
                                    <label class="flex items-center p-4 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition duration-200">
                                        <input type="radio" name="selected" value="{{ $option }}" class="mr-4 text-blue-600">
                                        <span class="text-lg">{{ $option }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                                @if($current_question == $total_questions)
                                    結果を見る
                                @else
                                    次の問題へ
                                @endif
                            </button>
                        </div>
                    </form>

                    {{-- 中断ボタン --}}
                    <div class="mt-4 text-center">
                        <a href="{{ route('quiz.finish') }}" class="text-gray-500 hover:text-gray-700 underline">
                            クイズを中断してホームに戻る
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>