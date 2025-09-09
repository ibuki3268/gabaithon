import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Laravel Echo for Pusher
try {
    // 動的インポートでEchoとPusherを読み込み
    const { default: Echo } = await import('laravel-echo');
    const { default: Pusher } = await import('pusher-js');

    // Pusherをグローバルに設定
    window.Pusher = Pusher;

    // Laravel Echoの設定
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap3',
        wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher-channels.com`,
        wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
        wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        // デバッグ用
        enableLogging: import.meta.env.DEV,
        // 認証が必要な場合のオプション
        authorizer: (channel, options) => {
            return {
                authorize: (socketId, callback) => {
                    fetch('/broadcasting/auth', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            socket_id: socketId,
                            channel_name: channel.name
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        callback(false, data);
                    })
                    .catch(error => {
                        console.error('認証エラー:', error);
                        callback(true, error);
                    });
                }
            };
        },
    });

    // デバッグ用：接続状態をコンソールに出力
    if (import.meta.env.DEV) {
        console.log('Laravel Echo初期化完了');
        console.log('Pusher設定:', {
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https'
        });
    }

} catch (error) {
    console.error('Laravel Echo初期化失敗:', error);
    window.Echo = null;
}