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
                    @else
                        {{-- 最初の問題を表示（今回はまず1問目だけ表示する簡単な作りにします） --}}
                        @php
                            $question = $questions->first();
                        @endphp

                        <h3 class="text-2xl font-bold mb-4">{{ $question->question_text }}</h3>

                        <form action="#" method="POST">
                            @csrf
                            <div class="space-y-4">
                                {{-- 選択肢をループで表示 --}}
                                @foreach($question->choices as $choice)
                                    <div>
                                        <label class="flex items-center p-4 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                            <input type="radio" name="choice" value="{{ $choice->id }}" class="mr-4">
                                            <span>{{ $choice->text }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="w-full bg-green-500 text-white font-bold py-3 px-4 rounded hover:bg-green-700 transition">
                                    回答する
                                </button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>