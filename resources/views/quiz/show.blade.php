<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- コントローラーから渡された牌のタイトルを表示 --}}
            {{ $tile->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- まだクイズデータがない場合の表示 --}}
                    @if($questions->isEmpty())
                        <p>このコースにはまだクイズがありません。</p>
                        <a href="{{ route('dashboard') }}" class="inline-block mt-4 text-blue-500 hover:underline">
                            &laquo; ダッシュボードに戻る
                        </a>
                    @else
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold">問題一覧</h3>
                            {{-- クイズ開始ボタン --}}
                            <a href="{{ route('quiz.start') }}" class="inline-block bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                                学習を開始する
                            </a>
                        </div>
                        
                        <div class="space-y-4">
                            {{-- 問題をループで表示 --}}
                            @foreach($questions as $question)
                                <div class="p-4 border rounded-lg">
                                    <p>{{ $question->question_text }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>