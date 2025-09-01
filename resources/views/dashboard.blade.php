<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-2xl font-bold mb-4">学習コース一覧</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($tiles as $tile)
                            <div class="bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 shadow">
                                <h3 class="font-bold text-lg">{{ $tile->title }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $tile->description }}</p>
                                <div class="text-right mt-4">
                                    {{-- ▼▼▼ この行が重要な変更点です ▼▼▼ --}}
                                    <a href="{{ route('quiz.show', ['tile' => $tile->id]) }}" class="bg-blue-500 text-white dark:text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition">
                                        挑戦する
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>