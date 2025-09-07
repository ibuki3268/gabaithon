<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('学習コース選択') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex gap-6">
                
                {{-- キャラ表示 --}}
                <div class="w-80">
                    <div class="bg-pink-300 p-6 rounded-lg">
                        <div class="text-center text-gray-800 font-medium">
                            ガチャ機能やキャラ実装したら使う
                        </div>
                    </div>
                </div>

                {{-- 右側のスクロールできるUI --}}
                <div class="flex-1">
                    <div class="bg-white dark:bg-gray-800 border-4 border-blue-500 rounded-lg shadow-lg">
                        {{-- 固定サイズのスクロール可能エリア --}}
                        <div class="h-96 overflow-y-auto p-6">
                            
                            <div x-data="{ 
                                step: 1, 
                                selectedCategory: '', 
                                selectedSubCategory: '',
                                breadcrumb: {
                                    1: 'モード選択',
                                    2: 'ジャンル選択', 
                                    3: '学習内容選択',
                                    4: '役名選択'
                                }
                            }">

                                {{-- パンくずリスト --}}
                                <div class="mb-4 text-sm text-gray-500 sticky top-0 bg-white dark:bg-gray-800 pb-2">
                                    <span x-text="breadcrumb[step]"></span>
                                    <span x-show="step > 1"> / </span>
                                    <span x-show="step > 1 && selectedCategory" x-text="selectedCategory"></span>
                                    <span x-show="step > 2"> / </span>
                                    <span x-show="step > 2 && selectedSubCategory" x-text="selectedSubCategory"></span>
                                </div>

                                {{-- Step 1: 牌山選択 --}}
                                <div x-show="step === 1" class="space-y-3">
                                    <h3 class="text-lg font-bold mb-4">牌山を選択してください</h3>
                                    <button @click="step = 2" class="w-full text-left p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
                                        <div class="font-semibold">既存牌山</div>
                                    </button>
                                    <button class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                        <div class="font-semibold">カスタム牌山</div>
                                    </button>
                                    <button class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                        <div class="font-semibold">自作牌山</div>
                                    </button>
                                </div>

                                {{-- Step 2: ジャンル選択 --}}
                                <div x-show="step === 2" class="space-y-3">
                                    <h3 class="text-lg font-bold mb-4">学習ジャンルを選択してください</h3>
                                    <button @click="step = 3; selectedCategory = '英検'" class="w-full text-left p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
                                        <div class="font-semibold">英検</div>
                                    </button>
                                    <button @click="step = 3; selectedCategory = '雀士'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                        <div class="font-semibold">雀士</div>
                                    </button>
                                    <button @click="step = 3; selectedCategory = 'エンジニア'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                        <div class="font-semibold">エンジニア</div>
                                    </button>
                                    <button @click="step = 3; selectedCategory = '社会'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                        <div class="font-semibold">社会</div>
                                    </button>
                                    <button @click="step = 1" class="mt-4 text-sm text-gray-500 hover:underline">
                                        &laquo; 戻る
                                    </button>
                                </div>

                                {{-- Step 3: 学習内容選択 --}}
                                <div x-show="step === 3" class="space-y-3">
                                    <h3 class="text-lg font-bold mb-4">学習内容を選択してください</h3>
                                    
                                    {{-- 英検 --}}
                                    <div x-show="selectedCategory === '英検'">
                                        <button @click="step = 4; selectedSubCategory = '英検準1級 1翻役'" class="w-full text-left p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition mb-3">
                                            <div class="font-semibold">英検準1級 1翻役</div>
                                        </button>
                                        <button @click="step = 4; selectedSubCategory = '英検1級 役満'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition mb-3">
                                            <div class="font-semibold">英検1級 役満</div>
                                        </button>
                                        <button @click="step = 4; selectedSubCategory = '英検2級 基礎'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition mb-3">
                                            <div class="font-semibold">英検2級 基礎</div>
                                        </button>
                                        <button @click="step = 4; selectedSubCategory = '英検3級 入門'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">英検3級 入門</div>
                                        </button>
                                    </div>

                                    {{-- 雀士 --}}
                                    <div x-show="selectedCategory === '雀士'">
                                        <button @click="step = 4; selectedSubCategory = '1翻役'" class="w-full text-left p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition mb-3">
                                            <div class="font-semibold">1翻役</div>
                                        </button>
                                        <button @click="step = 4; selectedSubCategory = '2翻役'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition mb-3">
                                            <div class="font-semibold">2翻役</div>
                                        </button>
                                        <button @click="step = 4; selectedSubCategory = '役満'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">役満</div>
                                        </button>
                                    </div>

                                    {{-- エンジニア --}}
                                    <div x-show="selectedCategory === 'エンジニア'">
                                        <button @click="step = 4; selectedSubCategory = 'フロントエンド'" class="w-full text-left p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition mb-3">
                                            <div class="font-semibold">フロントエンド</div>
                                        </button>
                                        <button @click="step = 4; selectedSubCategory = 'バックエンド'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">バックエンド</div>
                                        </button>
                                    </div>
                                    
                                    {{-- 社会 --}}
                                    <div x-show="selectedCategory === '社会'">
                                        <button @click="step = 4; selectedSubCategory = '歴史'" class="w-full text-left p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition mb-3">
                                            <div class="font-semibold">歴史</div>
                                        </button>
                                        <button @click="step = 4; selectedSubCategory = '地理'" class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">地理</div>
                                        </button>
                                    </div>

                                    <button @click="step = 2; selectedCategory = ''" class="mt-4 text-sm text-gray-500 hover:underline">
                                        &laquo; 戻る
                                    </button>
                                </div>

                                {{-- Step 4: 役名選択 --}}
                                <div x-show="step === 4" class="space-y-3">
                                    <h3 class="text-lg font-bold mb-4">学習する項目を選択してください</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" x-text="selectedCategory + ' > ' + selectedSubCategory"></p>
                                    
                                    {{-- ▼▼▼▼▼ ここを全て<a>タグに変更しました ▼▼▼▼▼ --}}
                                    <a href="{{ route('quiz.start') }}" class="block w-full text-left p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition mb-3">
                                        <div class="font-semibold">四槓子</div>
                                    </a>
                                    <a href="{{ route('quiz.start') }}" class="block w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition mb-3">
                                        <div class="font-semibold">大三元</div>
                                    </a>
                                    <a href="{{ route('quiz.start') }}" class="block w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition mb-3">
                                        <div class="font-semibold">国士無双</div>
                                    </a>
                                    <a href="{{ route('quiz.start') }}" class="block w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition mb-3">
                                        <div class="font-semibold">九蓮宝燈</div>
                                    </a>
                                    <a href="{{ route('quiz.start') }}" class="block w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition mb-3">
                                        <div class="font-semibold">緑一色</div>
                                    </a>
                                    <a href="{{ route('quiz.start') }}" class="block w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg transition">
                                        <div class="font-semibold">清老頭</div>
                                    </a>
                                    
                                    <button @click="step = 3; selectedSubCategory = ''" class="mt-4 text-sm text-gray-500 hover:underline">
                                        &laquo; 戻る
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            {{-- 獲得牌の表示 --}}
            <div class="mt-6">
                <div class="bg-orange-600 text-white text-center py-3 rounded-lg font-bold flex items-center justify-center">
                    <span>新規学習で牌を獲得しよう！・獲得牌の表示</span>
                    <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>