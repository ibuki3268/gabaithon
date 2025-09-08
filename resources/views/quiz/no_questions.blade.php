<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            クイズ
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">

                    <h3 class="text-2xl font-bold mb-6">問題が見つかりませんでした</h3>
                    <p class="mb-6">
                        このコースには、現在利用できるクイズがありません。<br>
                        別のコースを試すか、後でもう一度お試しください。
                    </p>
                    <a href="{{ route('dashboard') }}"
                       class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                        ダッシュボードに戻る
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>