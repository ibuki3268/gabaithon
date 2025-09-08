<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ガチャ') }}
        </h2>
    </x-slot>

    <div class="py-6 flex justify-center items-start min-h-screen">
        {{-- 麻雀卓風背景は body 側で mahjong.css に設定済み --}}
        <div class="bg-white p-6 rounded-2xl shadow-2xl w-96">
            {{-- タイトル --}}
            <h3 class="text-center text-gray-800 font-bold text-xl mb-6">ガチャを回す</h3>

            {{-- ガチャ結果表示枠 --}}
            <div id="gacha-display" class="bg-gray-100 rounded-lg h-64 flex items-center justify-center mb-6 shadow-inner">
                <span class="text-gray-500 text-center">ここにキャラや牌が表示されます</span>
            </div>

            {{-- 単発ガチャフォーム --}}
            <form id="gacha-form" action="{{ route('gacha.draw') }}" method="POST" class="mb-4">
                @csrf
                <input type="hidden" name="gacha_type" value="single">
                <button type="submit"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                    ガチャを回す（単発 100P）
                </button>
            </form>

            {{-- 10連ガチャフォーム --}}
            <form id="gacha-form-10" action="{{ route('gacha.draw') }}" method="POST">
                @csrf
                <input type="hidden" name="gacha_type" value="ten">
                <button type="submit"
                        class="w-full bg-indigo-400 hover:bg-indigo-500 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                    10連ガチャ（900P）
                </button>
            </form>

            {{-- 戻る --}}
            <a href="{{ route('dashboard') }}"
               class="block text-center mt-6 text-gray-800 underline hover:text-gray-600">
                戻る
            </a>
        </div>
    </div>
</x-app-layout>
