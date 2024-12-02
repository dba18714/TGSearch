#!/bin/bash

# 加载 deploy.env 文件
if [ ! -f deploy.env ]; then
    echo "❌ deploy.env 文件不存在！"
    exit 1
fi
export $(grep -v '^#' deploy.env | xargs)

# 定义颜色输出
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 定义检查函数
check_status() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ $1 执行成功${NC}"
    else
        echo -e "${RED}✗ $1 执行失败${NC}"
        exit 1
    fi
}

# Logo显示
cat << "EOF"
 _____      _                                                                 
|_   _|___ | |  ___   __ _  _ __  __ _  _ __ ___          _ __    __ _ __   __
  | | / _ \| | / _ \ / _` || '__|/ _` || '_ ` _ \  _____ | '_ \  / _` |\ \ / /
  | ||  __/| ||  __/| (_| || |  | (_| || | | | | ||_____|| | | || (_| | \ V / 
  |_| \___||_| \___| \__, ||_|   \__,_||_| |_| |_|       |_| |_| \__,_|  \_/  
                     |___/                                                     
EOF

# 更新包列表
echo "正在执行 apt-get update..."
apt-get update
check_status "apt-get update"

# 执行npm安装
echo "正在执行 npm 安装..."
source _deploy/install_npm.sh
check_status "NPM安装"

# 执行meilisearch安装
echo "正在执行 meilisearch 安装..."
source _deploy/install_meilisearch.sh
check_status "Meilisearch安装"

# 执行composer安装
echo "正在执行 composer 安装..."
source _deploy/install_composer.sh
check_status "Composer安装"

# 执行文件权限设置
echo "正在设置文件权限..."
source _deploy/file_permission.sh
check_status "文件权限设置"

# 执行composer依赖安装
echo "正在安装 composer 依赖..."
COMPOSER_ALLOW_SUPERUSER=1 php composer.phar install
check_status "Composer依赖安装"

# 执行应用安装
echo "正在执行应用安装..."
php artisan deploy:install \
        --DB_DATABASE=$DB_DATABASE \
        --DB_USERNAME=$DB_USERNAME \
        --DB_PASSWORD=$DB_PASSWORD \
        --MEILISEARCH_KEY=$MEILISEARCH_KEY \
        --ADMIN_EMAIL=$ADMIN_EMAIL \
        --ADMIN_PASSWORD=$ADMIN_PASSWORD 
check_status "应用安装"

# 执行npm依赖安装
echo "正在安装 npm 依赖..."
npm install
check_status "NPM依赖安装"

# 执行npm构建
echo "正在执行 npm 构建..."
npm run build
check_status "NPM构建"

echo -e "${GREEN}所有安装步骤已完成!${NC}"