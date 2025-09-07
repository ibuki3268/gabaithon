<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('捨て牌一覧') }}
        </h2>
    </x-slot>

    <div class="py-6">
    <div class="w-full mx-auto px-4">
            <div class="flex gap-6">

                <div x-data="{ selectedGenre: '英検', selectedCourse: '', showDiscardedTiles: false, currentCourseName: '' }" class="flex gap-6 w-full">

                    {{-- 左側：ジャンル選択 --}}
                    <div style="width: 500px; min-width: 500px;">
                        <div class="bg-white dark:bg-gray-800 border-2 border-blue-500 rounded-lg shadow-lg h-96 w-[250px] min-w-[250px]">
                            <div class="p-4">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">ジャンル</h3>
                                <div class="space-y-2">
                                {{-- ジャンルタブ --}}
                                <button @click="selectedGenre = '英検'" 
                                        :class="selectedGenre === '英検' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-300'"
                                        class="w-full text-left p-3 rounded-lg transition font-medium">
                                    英検
                                </button>
                                <button @click="selectedGenre = '雀士'" 
                                        :class="selectedGenre === '雀士' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-300'"
                                        class="w-full text-left p-3 rounded-lg transition font-medium">
                                    雀士
                                </button>
                                <button @click="selectedGenre = 'エンジニア'" 
                                        :class="selectedGenre === 'エンジニア' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-300'"
                                        class="w-full text-left p-3 rounded-lg transition font-medium">
                                    エンジニア
                                </button>
                                <button @click="selectedGenre = '社会'" 
                                        :class="selectedGenre === '社会' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
                                        class="w-full text-left p-3 rounded-lg transition font-medium">
                                    社会
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 右側：学習内容と捨て牌 --}}
                        <div class="flex-1 bg-white dark:bg-gray-800 border-2 border-blue-500 rounded-lg shadow-lg h-96">
                            <div class="h-full min-h-full overflow-y-auto p-6 pr-8">
                                <!-- 右側は親のx-dataの変数をそのまま利用 -->

                                {{-- パンくずリスト --}}
                                <div class="mb-4 text-sm text-gray-500 sticky top-0 bg-white dark:bg-gray-800 pb-2">
                                    <span x-text="selectedGenre"></span>
                                    <span x-show="selectedCourse"> / </span>
                                    <span x-show="selectedCourse" x-text="selectedCourse"></span>
                                    <span x-show="showDiscardedTiles"> / 捨て牌一覧</span>
                                </div>

                                {{-- 学習内容一覧 --}}
                                <div x-show="!showDiscardedTiles">
                                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">学習内容を選択してください</h3>
                                    {{-- 英検の学習内容 --}}
                                    <div x-show="selectedGenre === '英検'" class="space-y-3 min-h-full">
                                        <button @click="selectedCourse = '英検準1級 1翻役'; currentCourseName = '英検準1級 1翻役'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">英検準1級 1翻役</div>
                                            <div class="text-sm mt-1 opacity-90">基礎的な英語表現</div>
                                        </button>
                                        <button @click="selectedCourse = '英検1級 役満'; currentCourseName = '英検1級 役満'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">英検1級 役満</div>
                                            <div class="text-sm mt-1 text-gray-600">高難度な英語表現</div>
                                        </button>
                                        <button @click="selectedCourse = '英検2級 基礎'; currentCourseName = '英検2級 基礎'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">英検2級 基礎</div>
                                            <div class="text-sm mt-1 text-gray-600">中級レベル</div>
                                        </button>
                                    </div>

                                    {{-- 雀士の学習内容 --}}
                                    <div x-show="selectedGenre === '雀士'" class="space-y-3 min-h-full">
                                        <button @click="selectedCourse = '1翻役'; currentCourseName = '1翻役'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">1翻役</div>
                                            <div class="text-sm mt-1 opacity-90">基本的な役</div>
                                        </button>
                                        <button @click="selectedCourse = '2翻役'; currentCourseName = '2翻役'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">2翻役</div>
                                            <div class="text-sm mt-1 text-gray-600">中級の役</div>
                                        </button>
                                        <button @click="selectedCourse = '役満'; currentCourseName = '役満'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">役満</div>
                                            <div class="text-sm mt-1 text-gray-600">最高難度の役</div>
                                        </button>
                                    </div>

                                    {{-- エンジニアの学習内容 --}}
                                    <div x-show="selectedGenre === 'エンジニア'" class="space-y-3 min-h-full">
                                        <button @click="selectedCourse = 'フロントエンド'; currentCourseName = 'フロントエンド'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">フロントエンド</div>
                                            <div class="text-sm mt-1 opacity-90">HTML, CSS, JavaScript</div>
                                        </button>
                                        <button @click="selectedCourse = 'バックエンド'; currentCourseName = 'バックエンド'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">バックエンド</div>
                                            <div class="text-sm mt-1 text-gray-600">サーバー・データベース</div>
                                        </button>
                                    </div>

                                    {{-- 社会の学習内容 --}}
                                    <div x-show="selectedGenre === '社会'" class="space-y-3 min-h-full">
                                        <button @click="selectedCourse = '歴史'; currentCourseName = '歴史'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">歴史</div>
                                            <div class="text-sm mt-1 opacity-90">日本史・世界史</div>
                                        </button>
                                        <button @click="selectedCourse = '地理'; currentCourseName = '地理'; showDiscardedTiles = true" 
                                                class="w-full text-left p-4 bg-white border-2 border-gray-300 hover:bg-blue-500 text-gray-800 rounded-lg transition">
                                            <div class="font-semibold">地理</div>
                                            <div class="text-sm mt-1 text-gray-600">世界地理・日本地理</div>
                                        </button>
                                    </div>
                                </div>

                                {{-- 捨て牌一覧 --}}
                                <div x-show="showDiscardedTiles">
                                    <div class="flex items-center justify-between mb-4 min-h-full">
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">捨て牌一覧</h3>
                                        <button @click="showDiscardedTiles = false; selectedCourse = ''" 
                                                class="text-sm text-gray-500 hover:underline">
                                            &laquo; 学習内容に戻る
                                        </button>
                                    </div>
                                    
                                    <div class="text-sm text-gray-600 mb-4" x-text="currentCourseName + ' で獲得できなかった牌を再挑戦できます'"></div>

                                    {{-- 捨て牌グリッド --}}
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach($discardedTiles as $tile)
                                        <div class="bg-red-100 border-2 border-red-300 rounded-lg p-4 hover:shadow-lg transition">
                                            <div class="text-center">
                                                {{-- 牌の画像（将来的に実装） --}}
                                                <div class="w-16 h-20 bg-gray-200 rounded mx-auto mb-2 flex items-center justify-center">
                                                    <span class="text-xs text-gray-500">牌{{ $tile['tile_id'] }}</span>
                                                </div>
                                                
                                                {{-- 牌のID --}}
                                                <div class="font-semibold text-sm mb-1">牌ID: {{ $tile['tile_id'] }}</div>
                                                
                                                {{-- コースID --}}
                                                <div class="text-xs text-gray-600 mb-2">コース: {{ $tile['course_id'] }}</div>
                                                
                                                {{-- 作成日時 --}}
                                                <div class="text-xs text-red-600 mb-3">
                                                    {{ $tile['created_at'] }}
                                                </div>
                                                
                                                {{-- 再挑戦ボタン --}}
                                                <form action="{{ route('discarded.retry', $tile['id']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="w-full bg-red-500 hover:bg-red-600 text-white text-xs py-2 px-3 rounded transition">
                                                        再挑戦
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- 捨て牌がない場合のメッセージ --}}
                                    @if(count($discardedTiles) == 0)
                                    <div class="text-center py-8">
                                        <div class="text-gray-500 mb-2">捨て牌はありません</div>
                                        <div class="text-sm text-gray-400">すべての問題をクリアしています！</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>