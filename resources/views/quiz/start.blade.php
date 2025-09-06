<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('クイズを開始') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <h1 class="text-3xl font-bold mb-6">クイズスタート！</h1>
                        <p class="text-lg mb-8">準備はいいですか？クイズを開始しましょう！</p>
                        
                        <div class="space-y-4">
                            <a href="{{ route('quiz.show') }}" 
                               class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                クイズを開始する
                            </a>
                            
                            <br>
                            
                            <a href="{{ route('quiz.index') }}" 
                               class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                クイズ一覧に戻る
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>