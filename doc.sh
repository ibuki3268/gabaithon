#!/bin/bash

# 使用する前に権限を設定してください
# chmod +x doc.sh

#実行コマンド一覧

#使い方：./doc.sh
#実行：./doc.sh start
#削除：./doc.sh down


if [ "$1" == "start" ]; then
    ./vendor/bin/sail up -d
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run dev
    echo "アプリを立ち上げました。"
elif [ "$1" == "down" ]; then
    ./vendor/bin/sail down
    echo "Docker コンテナを削除しました。"
else
    echo "使い方: ./doc.sh start|down"
fi
