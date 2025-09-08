<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            対戦画面（サンプル）
        </h2>
    </x-slot>

    <div class="p-4 space-y-4">
        <!-- ゲーム情報表示 -->
        <div class="bg-blue-100 dark:bg-blue-800 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="font-bold">現在の手番: {{ $currentPlayer }}</span>
                <span>牌山残り: {{ count($wall) }}枚</span>
                @if(isset($turnCount))
                    <span>ターン: {{ $turnCount }}</span>
                @endif
            </div>
        </div>

        <!-- メッセージ表示 -->
        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if(session('winner'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                🎉 {{ session('winner') }} の勝利！！ 🎉
            </div>
        @endif

        @foreach($hands as $player => $tiles)
            <div class="border rounded p-4 {{ $player === $currentPlayer ? 'bg-yellow-100 dark:bg-yellow-800' : 'bg-gray-100 dark:bg-gray-800' }}">
                <h3 class="font-bold mb-2 flex items-center">
                    {{ $player }}の手牌 ({{ count($tiles) }}枚)
                    @if($player === $currentPlayer)
                        <span class="ml-2 bg-green-500 text-white px-2 py-1 rounded text-xs">手番</span>
                    @endif
                </h3>
                
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach($tiles as $index => $tile)
                        <div class="border rounded p-2 bg-white dark:bg-gray-700 text-center min-w-[60px] relative">
                            <div class="text-sm font-bold">{{ $tile }}</div>
                            
                            @if($player === $currentPlayer)
                                <form action="{{ route('vs.drawAndDiscard', ['player' => $player, 'tileIndex' => $index]) }}" method="POST" class="mt-1">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 bg-red-100 px-2 py-1 rounded hover:bg-red-200">
                                        捨てる
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                <h4 class="font-semibold mb-2 text-sm">{{ $player }}の捨て牌 ({{ count($discards[$player]) }}枚)</h4>
                <div class="flex flex-wrap gap-1 mb-2">
                    @foreach($discards[$player] as $discard)
                        <span class="border rounded p-1 bg-gray-200 dark:bg-gray-600 text-xs min-w-[50px] text-center">{{ $discard }}</span>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- 牌山表示 -->
        <div class="mt-4 p-4 border rounded bg-gray-200 dark:bg-gray-800">
            <h3 class="font-bold mb-2">牌山（残り {{ count($wall) }} 枚）</h3>
            <div class="flex flex-wrap gap-1">
                @for($i = 0; $i < min(count($wall), 20); $i++)
                    <span class="border rounded p-1 bg-white dark:bg-gray-700 text-xs w-8 h-8 flex items-center justify-center">?</span>
                @endfor
                @if(count($wall) > 20)
                    <span class="text-sm text-gray-600 ml-2">... 他{{ count($wall) - 20 }}枚</span>
                @endif
            </div>
        </div>

        <!-- コントロールパネル -->
        <div class="flex gap-4 mt-4">
            <!-- リセットボタン -->
            <form action="{{ route('vs.reset') }}" method="GET">
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    ゲームリセット
                </button>
            </form>

            <!-- AI自動プレイボタン（将来実装用） -->
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" disabled>
                AI自動プレイ（準備中）
            </button>

            <!-- ゲーム統計表示ボタン -->
            <button onclick="showStats()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                統計表示
            </button>
        </div>

        <!-- 統計表示エリア -->
        <div id="statsArea" class="hidden mt-4 p-4 border rounded bg-blue-50 dark:bg-blue-900">
            <h4 class="font-bold mb-2">ゲーム統計</h4>
            <div id="statsContent"></div>
        </div>
    </div>

    <script>
        function showStats() {
            const statsArea = document.getElementById('statsArea');
            const statsContent = document.getElementById('statsContent');
            
            // 簡単な統計情報を表示
            const playerCounts = @json(array_map('count', $hands));
            const discardCounts = @json(array_map('count', $discards));
            const wallCount = {{ count($wall) }};
            const currentPlayer = '{{ $currentPlayer }}';
            
            let statsHtml = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h5 class="font-semibold">手牌数</h5>
                        <ul class="text-sm">
                            ${Object.entries(playerCounts).map(([player, count]) => 
                                `<li>${player}: ${count}枚 ${player === currentPlayer ? '(手番)' : ''}</li>`
                            ).join('')}
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-semibold">捨て牌数</h5>
                        <ul class="text-sm">
                            ${Object.entries(discardCounts).map(([player, count]) => 
                                `<li>${player}: ${count}枚</li>`
                            ).join('')}
                        </ul>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm">牌山残り: ${wallCount}枚</p>
                    <p class="text-sm">総消費牌: ${Object.values(discardCounts).reduce((a, b) => a + b, 0)}枚</p>
                </div>
            `;
            
            statsContent.innerHTML = statsHtml;
            statsArea.classList.toggle('hidden');
        }

        // 自動リフレッシュ（開発用）
        // setInterval(() => {
        //     if (confirm('ページを更新しますか？')) {
        //         location.reload();
        //     }
        // }, 30000);
    </script>
</x-app-layout>