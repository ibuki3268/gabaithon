<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            クイズ結果
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center text-gray-900">

                    <h3 class="text-3xl font-bold text-blue-500 mb-6">お疲れ様でした！</h3>

                    <div class="text-xl mb-8">
                        <p>
                            {{-- 総問題数と正解数を表示 --}}
                            <span class="font-bold text-2xl">{{ $total }}</span> 問中
                            <span class="font-bold text-2xl text-green-500">{{ $correctCount }}</span> 問正解です！
                        </p>
                    </div>

                    <div class="flex justify-center space-x-4">
                        {{-- もう一度挑戦するリンク --}}
                        <a href="{{ route('quiz.start') }}"
                           class="px-6 py-3 font-semibold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-300">
                            もう一度挑戦する
                        </a>

                        {{-- トップページに戻るリンク --}}
                        <a href="{{ route('dashboard') }}"
                           class="px-6 py-3 font-semibold text-gray-800 bg-gray-200 rounded-lg shadow-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition duration-300">
                            トップページへ戻る
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>