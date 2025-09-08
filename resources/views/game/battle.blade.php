<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            対戦画面
        </h2>
    </x-slot>

    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 p-4">
        <!-- 自分の手牌 -->
        <div class="flex-1 border rounded p-4 bg-gray-100 dark:bg-gray-800">
            <h3 class="font-bold mb-2">あなたの手牌</h3>
            <div class="flex flex-wrap space-x-2">
                @foreach($tiles as $tile)
                    <div class="border rounded p-2 bg-white dark:bg-gray-700 text-center">
                        {{ $tile->name }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 捨て牌エリア -->
        <div class="flex-1 border rounded p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="font-bold mb-2">捨て牌</h3>
            <div class="flex flex-wrap space-x-2">
                <!-- 将来的に GameLog から捨て牌を表示 -->
                <span class="border rounded p-2 bg-white dark:bg-gray-700">一萬</span>
                <span class="border rounded p-2 bg-white dark:bg-gray-700">九索</span>
            </div>
        </div>
    </div>

    <!-- 牌山表示（例） -->
    <div class="mt-4 p-4 border rounded bg-gray-200 dark:bg-gray-800">
        <h3 class="font-bold mb-2">牌山（残り）</h3>
        <div class="flex flex-wrap space-x-1">
            <span class="border rounded p-1 bg-white dark:bg-gray-700">?</span>
            <span class="border rounded p-1 bg-white dark:bg-gray-700">?</span>
            <span class="border rounded p-1 bg-white dark:bg-gray-700">?</span>
            <!-- 残り牌はここに表示 -->
        </div>
    </div>
</x-app-layout>
