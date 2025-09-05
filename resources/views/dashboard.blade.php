<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('対戦モード選択') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div x-data="{ step: 1 }">

                        {{-- Step 1: 大分類のボタン --}}
                        <div x-show="step === 1" class="space-y-4">
                            <h3 class="text-lg font-bold">モードを選択してください</h3>
                            <button @click="step = 2" class="w-full text-left p-4 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition">
                                新規学習
                            </button>
                            {{-- ▼▼ 変更点：クリックできないように無効化 ▼▼ --}}
                            <button disabled class="w-full text-left p-4 bg-gray-200 dark:bg-gray-700 rounded-lg transition opacity-50 cursor-not-allowed">
                                既存牌山 (未実装)
                            </button>
                             <button disabled class="w-full text-left p-4 bg-gray-200 dark:bg-gray-700 rounded-lg transition opacity-50 cursor-not-allowed">
                                カスタム牌山 (未実装)
                            </button>
                        </div>

                        {{-- Step 2: 中分類のボタン --}}
                        <div x-show="step === 2" class="space-y-4">
                            <h3 class="text-lg font-bold">部屋を選択してください</h3>
                             <button @click="step = 3" class="w-full text-left p-4 bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 rounded-lg transition">
                                英検
                            </button>
                            <button @click="step = 3" class="w-full text-left p-4 bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 rounded-lg transition">
                                雀士
                            </button>
                            <button @click="step = 1" class="mt-4 text-sm text-gray-500 hover:underline">
                                &laquo; 戻る
                            </button>
                        </div>

                        {{-- Step 3: 小分類のボタン --}}
                        <div x-show="step === 3" class="space-y-4">
                            <h3 class="text-lg font-bold">部屋を選択してください</h3>
                             <button @click="step = 4" class="w-full text-left p-4 bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 rounded-lg transition">
                                英検〇級１翻役
                            </button>
                            <button @click="step = 4" class="w-full text-left p-4 bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 rounded-lg transition">
                                英検〇級役満
                            </button>
                            <button @click="step = 2" class="mt-4 text-sm text-gray-500 hover:underline">
                                &laquo; 戻る
                            </button>
                        </div>

                        {{-- Step 4: 最終確認のボタン --}}
                        <div x-show="step === 4" class="space-y-4">
                            <h3 class="text-lg font-bold">最終確認</h3>
                            <p>役名選択</p>
                            <button @click="alert('学習を開始します！')" class="w-full text-left p-4 bg-red-200 dark:bg-red-800 hover:bg-red-300 dark:hover:bg-red-700 rounded-lg transition">
                                四槓子
                            </button>
                             <button @click="step = 3" class="mt-4 text-sm text-gray-500 hover:underline">
                                &laquo; 戻る
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>