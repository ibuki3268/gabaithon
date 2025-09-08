<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('学習コース選択') }}
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex gap-6">

                {{-- メインコンテンツ --}}
                {{-- キャラ表示 --}}
                <div class="w-80">
                    <div class="bg-pink-300 p-6 rounded-lg">
                        <div class="text-center text-gray-800 font-medium">
                            ガチャ機能やキャラ実装したら使う
                        </div>
                    </div>
                </div>
                {{-- キャラ表示 --}}
                {{--変更する画面--}}
                    <div class="flex-1">
                        <div class="bg-white dark:bg-gray-800 border-4 border-blue-500 rounded-lg shadow-lg">
                            {{-- 固定サイズのスクロール可能エリア --}}
                            <div id="change" 
                                data-selecthai-url="{{ route('selecthai') }}"
                                data-selectyaku-url="{{ route('selectyaku') }}"
                                data-selectdifficulty-url="{{ route('selectdifficulty') }}"
                                data-selectcourse-url="{{ route('selectcourse') }}"
                                data-dashboard-url="{{ route('dashboard') }}"
                                data-csrf-token="{{ csrf_token() }}"
                                class="h-96 overflow-y-auto px-6 pb-8">
                                <div x-data="{ hasProgress: @json($hasProgress) }">
                                    <h3 class="text-lg font-bold mb-4">学習モードを選択してください</h3>
                                    <button @click="selectcourse('new')"
                                        class="w-full text-left p-4 rounded-lg transition bg-orange-500 hover:bg-orange-600 text-white">
                                        <div class="font-semibold">新規学習</div>
                                        <div class="text-sm opacity-75">新しいコースを開始する</div>
                                    </button>
                                    <button @click="selectcourse('existing')"
                                            :class="hasProgress ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                            class="w-full text-left p-4 rounded-lg transition"
                                            :disabled="!hasProgress">
                                        <div class="font-semibold">既存学習</div>
                                        <div class="text-sm opacity-75">学習途中のコースを続ける</div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                
                {{--変更する画面--}}

                {{-- メインコンテンツ --}}
            </div>
        </div>
    </div>




</x-app-layout>