<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    /**
     * フレンド一覧画面を表示
     */
    public function index()
    {
        $user = Auth::user();
        
        // 仮データ：フレンド一覧
        $friends = [
            [
                'id' => 1,
                'name' => '田中太郎',
                'level' => 15,
                'last_login' => '2025-09-08 14:30:00',
                'status' => 'online',
                'avatar' => '/images/avatars/avatar1.png'
            ],
            [
                'id' => 2,
                'name' => '佐藤花子',
                'level' => 23,
                'last_login' => '2025-09-07 18:45:00',
                'status' => 'offline',
                'avatar' => '/images/avatars/avatar2.png'
            ],
            [
                'id' => 3,
                'name' => '鈴木一郎',
                'level' => 8,
                'last_login' => '2025-09-08 09:15:00',
                'status' => 'online',
                'avatar' => '/images/avatars/avatar3.png'
            ]
        ];
        
        // 仮データ：フレンド申請（受信）
        $friendRequests = [
            [
                'id' => 1,
                'from_user' => [
                    'id' => 4,
                    'name' => '山田次郎',
                    'level' => 12,
                    'avatar' => '/images/avatars/avatar4.png'
                ],
                'created_at' => '2025-09-07 16:20:00'
            ],
            [
                'id' => 2,
                'from_user' => [
                    'id' => 5,
                    'name' => '高橋美咲',
                    'level' => 19,
                    'avatar' => '/images/avatars/avatar5.png'
                ],
                'created_at' => '2025-09-06 12:30:00'
            ]
        ];
        
        // 仮データ：送信中のフレンド申請
        $sentRequests = [
            [
                'id' => 3,
                'to_user' => [
                    'id' => 6,
                    'name' => '伊藤健太',
                    'level' => 25,
                    'avatar' => '/images/avatars/avatar6.png'
                ],
                'created_at' => '2025-09-08 10:00:00'
            ]
        ];
        
        return view('friends', compact('friends', 'friendRequests', 'sentRequests'));
    }

    /**
     * フレンド検索
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // 仮データ：検索結果
        $searchResults = [];
        if ($query) {
            $searchResults = [
                [
                    'id' => 7,
                    'name' => '渡辺翔太',
                    'level' => 14,
                    'avatar' => '/images/avatars/avatar7.png',
                    'is_friend' => false,
                    'request_sent' => false
                ],
                [
                    'id' => 8,
                    'name' => '小林優子',
                    'level' => 21,
                    'avatar' => '/images/avatars/avatar8.png',
                    'is_friend' => false,
                    'request_sent' => true
                ]
            ];
        }
        
        return response()->json($searchResults);
    }

    /**
     * フレンド申請を送信
     */
    public function sendRequest(Request $request, $userId)
    {
        // 実際の実装では以下の処理を行う：
        // 1. 既にフレンドかチェック
        // 2. 既に申請を送信済みかチェック
        // 3. フレンド申請をデータベースに保存
        
        return response()->json([
            'success' => true,
            'message' => 'フレンド申請を送信しました'
        ]);
    }

    /**
     * フレンド申請を承認
     */
    public function acceptRequest(Request $request, $requestId)
    {
        // 実際の実装では以下の処理を行う：
        // 1. フレンド申請の存在確認
        // 2. フレンド関係をデータベースに追加
        // 3. フレンド申請を削除
        
        return redirect()->route('friends.index')
            ->with('success', 'フレンド申請を承認しました');
    }

    /**
     * フレンド申請を拒否
     */
    public function rejectRequest(Request $request, $requestId)
    {
        // 実際の実装では以下の処理を行う：
        // 1. フレンド申請の存在確認
        // 2. フレンド申請を削除
        
        return redirect()->route('friends')
            ->with('success', 'フレンド申請を拒否しました');
    }

    /**
     * フレンドを削除
     */
    public function removeFriend(Request $request, $friendId)
    {
        // 実際の実装では以下の処理を行う：
        // 1. フレンド関係の存在確認
        // 2. フレンド関係をデータベースから削除
        
        return redirect()->route('friends')
            ->with('success', 'フレンドを削除しました');
    }
}