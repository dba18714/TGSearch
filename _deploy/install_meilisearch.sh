#!/bin/bash

curl -L https://install.meilisearch.com | sh

cat >/etc/systemd/system/meilisearch.service <<EOF
[Unit]
Description=Meilisearch
After=systemd-user-sessions.service

[Service]
Type=simple
ExecStart=$(pwd)/meilisearch --http-addr 127.0.0.1:7700 --env production --master-key A4300576306E42D7

[Install]
WantedBy=default.target
EOF

systemctl enable meilisearch
systemctl start meilisearch
result=$(systemctl is-active meilisearch)

# result: active or inactive
if [[ $result = "active" ]]; then
    echo "Meilisearch 已启动"
else
    echo "Meilisearch 启动失败"
    exit 1
fi
