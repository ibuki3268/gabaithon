<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            対戦画面（サンプル）
        </h2>
    </x-slot>

    <div class="p-4 space-y-4">

        <!-- 各プレイヤーの手牌と捨て牌 -->
        @foreach($hands as $player => $tiles)
            <div class="border rounded p-4 bg-gray-100 dark:bg-gray-800">
                <h3 class="font-bold mb-2">{{ $player }}の手牌</h3>
                <div class="flex flex-wrap space-x-2 mb-2">
                    @foreach($tiles as $tile)
                        <div class="border rounded p-2 bg-white dark:bg-gray-700 text-center">
                            {{ $tile }}
                        </div>
                    @endforeach
                </div>

                <h3 class="font-bold mb-2">{{ $player }}の捨て牌</h3>
                <div class="flex flex-wrap space-x-2 mb-2">
                    @foreach($discards[$player] as $discard)
                        <span class="border rounded p-2 bg-white dark:bg-gray-700">{{ $discard }}</span>
                    @endforeach
                </div>

                @if($player === 'player1')
                    <!-- 自分だけ山から引くボタン -->
                    <form action="{{ route('vs.draw', $player) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            山から引く
                        </button>
                    </form>
                @endif
            </div>
        @endforeach

        <!-- 牌山（残り） -->
        <div class="mt-4 p-4 border rounded bg-gray-200 dark:bg-gray-800">
            <h3 class="font-bold mb-2">牌山（残り {{ count($wall) }} 枚）</h3>
            <div class="flex flex-wrap space-x-1">
                @foreach($wall as $tile)
                    <span class="border rounded p-1 bg-white dark:bg-gray-700">?</span>
                @endforeach
            </div>
        </div>

        <a href="{{ route('vs.reset') }}" 
   class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
   リセット
</a>


    </div>
</x-app-layout>
