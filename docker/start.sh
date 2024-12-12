#!/bin/sh
set -e

# 等待数据库准备就绪
until nc -z -v -w30 pgsql 5432; do
    echo "Waiting for database connection..."
    sleep 2
done

# 运行迁移和其他需要数据库的命令
php artisan deploy:migrate

# 启动 PHP-FPM
exec php-fpm