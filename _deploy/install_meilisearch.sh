#!/bin/bash

set -e  # 遇到错误立即退出
exec 2>&1  # 将错误输出重定向到标准输出

echo "开始安装 Meilisearch..."

# 停止已存在的服务
echo "1. 停止已存在的服务..."
sudo systemctl stop meilisearch 2>/dev/null || true
echo "✓ 服务停止完成"

# 安装 Meilisearch
echo "2. 下载 Meilisearch..."
curl -L https://install.meilisearch.com | sh
echo "✓ Meilisearch 下载完成"

# 检查二进制文件
echo "3. 检查二进制文件..."
if [ ! -f ./meilisearch ]; then
    echo "❌ Meilisearch 二进制文件不存在"
    exit 1
fi

# 测试二进制文件
echo "4. 测试二进制文件..."
chmod +x ./meilisearch
./meilisearch --version || {
    echo "❌ Meilisearch 二进制文件测试失败"
    exit 1
}
echo "✓ 二进制文件测试成功"

# 创建必要的目录
echo "5. 创建必要的目录..."
sudo mkdir -p /var/lib/meilisearch/data
sudo mkdir -p /etc/meilisearch
echo "✓ 目录创建完成"

# 确保没有进程在使用文件
echo "6. 确保没有进程在使用文件..."
sudo lsof /usr/bin/meilisearch 2>/dev/null | awk 'NR>1 {print $2}' | xargs -r sudo kill -9 || true
sleep 2
echo "✓ 进程清理完成"

# 移动二进制文件到标准位置
echo "7. 移动二进制文件..."
sudo cp ./meilisearch /usr/bin/meilisearch || {
    echo "❌ 复制文件失败，尝试使用 mv"
    sudo mv ./meilisearch /usr/bin/meilisearch
}
sudo chmod 755 /usr/bin/meilisearch
echo "✓ 二进制文件移动完成"

# 验证安装的二进制文件
echo "8. 验证安装的二进制文件..."
/usr/bin/meilisearch --version || {
    echo "❌ 安装的 Meilisearch 二进制文件验证失败"
    exit 1
}
echo "✓ 安装的二进制文件验证成功"

# 设置适当的权限
echo "9. 设置权限..."
sudo chown -R www-data:www-data /var/lib/meilisearch
sudo chmod 755 /var/lib/meilisearch
sudo chmod 755 /var/lib/meilisearch/data
echo "✓ 权限设置完成"

# 创建服务配置
echo "10. 创建服务配置..."
cat << EOF > meilisearch.service
[Unit]
Description=Meilisearch
After=systemd-user-sessions.service

[Service]
Type=simple
User=www-data
Group=www-data
ExecStart=/usr/bin/meilisearch --db-path /var/lib/meilisearch/data --http-addr 127.0.0.1:7700 --env production --master-key A4300576306E42D7
Restart=always
RestartSec=10

[Install]
WantedBy=default.target
EOF
sudo mv meilisearch.service /etc/systemd/system/
echo "✓ 服务配置创建完成"

# 重新加载 systemd 配置
echo "11. 重新加载 systemd 配置..."
sudo systemctl daemon-reload
echo "✓ systemd 配置重新加载完成"

# 启用并启动服务
echo "12. 启用并启动服务..."
sudo systemctl enable meilisearch || {
    echo "服务启用失败"
    exit 1
}

sudo systemctl start meilisearch || {
    echo "服务启动失败"
    sudo systemctl status meilisearch
    exit 1
}
echo "✓ 服务启用并启动"

# 检查服务状态
echo "13. 检查服务状态..."
for i in {1..6}; do
    if systemctl is-active meilisearch >/dev/null 2>&1; then
        echo "✓ Meilisearch 服务已激活"
        
        # 等待服务完全启动
        echo "14. 等待服务响应..."
        sleep 5
        
        if curl -s "http://127.0.0.1:7700/health" > /dev/null; then
            echo "✓ Meilisearch 运行正常"
            exit 0
        else
            echo "❌ Meilisearch 启动但未正常响应"
            sudo systemctl status meilisearch
            sudo journalctl -u meilisearch --no-pager -n 50
            exit 1
        fi
    fi
    echo "等待服务启动... ($i/6)"
    sleep 5
done

echo "❌ Meilisearch 服务启动失败"
sudo systemctl status meilisearch
sudo journalctl -u meilisearch --no-pager -n 50
exit 1