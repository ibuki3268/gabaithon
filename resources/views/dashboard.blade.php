<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('学習コース選択') }}
        </h2>
    </x-slot>
    <div class="py-6 h-screen flex flex-col">
        <div class="max-w-7xl mx-auto px-4 flex-1 flex">
            <div class="flex gap-6 w-full h-full">

                {{-- キャラ表示 --}}
                <div class="flex items-center justify-center">
                    <div class="w-80 text-center">
                        <!-- キャラクター画像 -->
                        <img src="/assets/character/tobakurou.png" 
                            alt="Tobakurou" 
                            class="mx-auto mb-4 rounded-lg shadow-lg">
                    </div>
                </div>

                {{-- カテゴリー選択画面 --}}
                <div class="flex-1 flex flex-col h-screen">
                    <div class="bg-white dark:bg-gray-800 border-2 border-white rounded-lg shadow-lg flex-1">
                        <div id="change" class="flex-1 overflow-y-auto px-6 pb-8">
                            
                            <h3 class="text-lg font-bold m-4">カテゴリーを選択してください</h3>
                            
                            {{-- カテゴリーボタン一覧 --}}
                            <div class="space-y-3">
                                @forelse($categories as $category)
                                    <button onclick="window.location.href='{{ route('quiz.start', ['category_id' => $category->id]) }}'" 
                                            class="w-full text-left p-4 rounded-lg transition bg-green-500 hover:bg-green-600 text-white shadow-md">
                                        <div class="font-semibold">{{ $category->categoryname }}</div>
                                        <div class="text-sm opacity-75">このカテゴリーで学習を開始</div>
                                    </button>
                                @empty
                                    <div class="text-center py-8 text-gray-500">
                                        <p>利用可能なカテゴリーがありません</p>
                                    </div>
                                @endforelse
                            </div>
                            
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>