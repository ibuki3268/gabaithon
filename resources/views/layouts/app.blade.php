<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>

        <!-- Pusher接続状態インジケーター -->
        <div id="connection-status" class="fixed bottom-4 right-4 z-50">
            <div id="connection-indicator" class="px-3 py-1 rounded-full text-xs font-medium transition-all duration-300">
                <span id="connection-text">接続中...</span>
            </div>
        </div>

        <!-- Laravel Echo初期化スクリプト -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 接続状態インジケーターの要素
                const indicator = document.getElementById('connection-indicator');
                const text = document.getElementById('connection-text');
                
                // Laravel Echoの接続状態を監視
                if (window.Echo) {
                    console.log('Laravel Echo初期化中...');
                    
                    // 接続成功
                    window.Echo.connector.pusher.connection.bind('connected', function() {
                        console.log('Pusher接続成功');
                        indicator.className = 'px-3 py-1 rounded-full text-xs font-medium bg-green-500 text-white transition-all duration-300';
                        text.textContent = '🟢 オンライン';
                        
                        // 3秒後にインジケーターを非表示
                        setTimeout(() => {
                            document.getElementById('connection-status').style.opacity = '0';
                        }, 3000);
                    });
                    
                    // 接続失敗
                    window.Echo.connector.pusher.connection.bind('failed', function() {
                        console.error('Pusher接続失敗');
                        indicator.className = 'px-3 py-1 rounded-full text-xs font-medium bg-red-500 text-white transition-all duration-300';
                        text.textContent = '🔴 接続失敗';
                    });
                    
                    // 切断
                    window.Echo.connector.pusher.connection.bind('disconnected', function() {
                        console.warn('Pusher接続切断');
                        indicator.className = 'px-3 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white transition-all duration-300';
                        text.textContent = '🟡 切断';
                    });
                    
                    // 再接続中
                    window.Echo.connector.pusher.connection.bind('connecting', function() {
                        console.log('Pusher再接続中...');
                        indicator.className = 'px-3 py-1 rounded-full text-xs font-medium bg-blue-500 text-white transition-all duration-300';
                        text.textContent = '🔵 再接続中...';
                    });
                    
                    // エラーハンドリング
                    window.Echo.connector.pusher.connection.bind('error', function(error) {
                        console.error('Pusher接続エラー:', error);
                        indicator.className = 'px-3 py-1 rounded-full text-xs font-medium bg-red-500 text-white transition-all duration-300';
                        text.textContent = '🔴 エラー';
                    });
                    
                } else {
                    console.error('Laravel Echoが見つかりません。Pusher設定を確認してください。');
                    indicator.className = 'px-3 py-1 rounded-full text-xs font-medium bg-red-500 text-white transition-all duration-300';
                    text.textContent = '🔴 Echo未対応';
                }
            });
        </script>

        <!-- グローバルスタイル -->
        <style>
            #connection-status {
                transition: opacity 0.3s ease-in-out;
            }
            
            /* 通知アニメーション */
            @keyframes pulse {
                0%, 100% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.7;
                }
            }
            
            .animate-pulse {
                animation: pulse 2s infinite;
            }
            
            /* カスタムスクロールバー */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }
            
            ::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 4px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #a1a1a1;
            }
            
            /* ダークモード対応 */
            .dark ::-webkit-scrollbar-track {
                background: #374151;
            }
            
            .dark ::-webkit-scrollbar-thumb {
                background: #6b7280;
            }
            
            .dark ::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
        </style>
    </body>
</html>