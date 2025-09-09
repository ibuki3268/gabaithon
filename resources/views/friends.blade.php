<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('フレンド') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full mx-auto px-4">
            <div class="flex gap-6">
                <div x-data="{ 
                    selectedTab: 'friends', 
                    searchQuery: '',
                    searchResults: []
                }" class="flex gap-6 w-full">

                    {{-- 左側：フレンド機能タブ --}}
                    <div style="width: 500px; min-width: 500px;">
                        <div class="bg-white dark:bg-gray-800 border-2 border-green-500 rounded-lg shadow-lg h-96">
                            <div class="h-full overflow-y-auto p-4">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200 sticky top-0 bg-white dark:bg-gray-800 pb-2">フレンド機能</h3>
                                <div class="space-y-2">
                                    {{-- フレンド一覧タブ --}}
                                    <button @click="selectedTab = 'friends'" 
                                            :class="selectedTab === 'friends' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-300'"
                                            class="w-full text-left p-3 rounded-lg transition font-medium">
                                        <div class="flex items-center justify-between">
                                            <span>フレンド一覧</span>
                                            <span class="text-sm bg-red-500 text-white px-2 py-1 rounded-full">{{ count($friends) }}</span>
                                        </div>
                                    </button>
                                    
                                    {{-- フレンド申請（受信）タブ --}}
                                    <button @click="selectedTab = 'requests'" 
                                            :class="selectedTab === 'requests' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-300'"
                                            class="w-full text-left p-3 rounded-lg transition font-medium">
                                        <div class="flex items-center justify-between">
                                            <span>受信申請</span>
                                            @if(count($friendRequests) > 0)
                                            <span class="text-sm bg-red-500 text-white px-2 py-1 rounded-full">{{ count($friendRequests) }}</span>
                                            @endif
                                        </div>
                                    </button>
                                    
                                    {{-- フレンド検索タブ --}}
                                    <button @click="selectedTab = 'search'" 
                                            :class="selectedTab === 'search' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-300'"
                                            class="w-full text-left p-3 rounded-lg transition font-medium">
                                        フレンド検索
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 右側：選択したタブのコンテンツ --}}
                    <div class="flex-1 bg-white dark:bg-gray-800 border-2 border-green-500 rounded-lg shadow-lg h-96">
                        <div class="h-full overflow-y-auto p-6 pr-8">

                            {{-- フレンド一覧 --}}
                            <div x-show="selectedTab === 'friends'">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">フレンド一覧</h3>
                                <div class="space-y-3">
                                    @foreach($friends as $friend)
                                    <div class="bg-gray-50 border-2 border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                {{-- アバター --}}
                                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-bold">{{ substr($friend['name'], 0, 1) }}</span>
                                                </div>
                                                
                                                {{-- ユーザー情報 --}}
                                                <div>
                                                    <div class="font-semibold text-gray-800 dark:text-gray-800">{{ $friend['name'] }}</div>
                                                    <div class="text-sm text-gray-600">Lv.{{ $friend['level'] }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        @if($friend['status'] === 'online')
                                                            <span class="text-green-500">● オンライン</span>
                                                        @else
                                                            最終ログイン: {{ \Carbon\Carbon::parse($friend['last_login'])->format('m/d H:i') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- アクションボタン --}}
                                            <div class="space-x-2">
                                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                                                    メッセージ
                                                </button>
                                                <form action="{{ route('friends.remove', $friend['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition"
                                                            onclick="return confirm('フレンドを削除しますか？')">
                                                        削除
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- 受信申請 --}}
                            <div x-show="selectedTab === 'requests'">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">フレンド申請（受信）</h3>
                                @if(count($friendRequests) > 0)
                                <div class="space-y-3">
                                    @foreach($friendRequests as $request)
                                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-bold">{{ substr($request['from_user']['name'], 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800">{{ $request['from_user']['name'] }}</div>
                                                    <div class="text-sm text-gray-600">Lv.{{ $request['from_user']['level'] }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($request['created_at'])->format('m/d H:i') }}</div>
                                                </div>
                                            </div>
                                            <div class="space-x-2">
                                                <form action="{{ route('friends.accept', $request['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition">
                                                        承認
                                                    </button>
                                                </form>
                                                <form action="{{ route('friends.reject', $request['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">
                                                        拒否
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-8">
                                    <div class="text-gray-500 mb-2">新しいフレンド申請はありません</div>
                                </div>
                                @endif
                            </div>

                            {{-- フレンド検索 --}}
                            <div x-show="selectedTab === 'search'">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">フレンド検索</h3>
                                <div class="mb-4">
                                    <input type="text" 
                                           x-model="searchQuery"
                                           placeholder="ユーザー名で検索"
                                           class="w-full p-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none">
                                </div>
                                <button @click="alert('検索機能は開発中です')" 
                                        class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg transition mb-4">
                                    検索
                                </button>
                                
                                <div class="text-center py-8">
                                    <div class="text-gray-500 mb-2">ユーザー名を入力して検索してください</div>
                                    <div class="text-sm text-gray-400">完全一致または部分一致で検索できます</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>