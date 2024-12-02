#!/bin/bash

if [ ! -e "composer.phar" ]; then
    echo "正在下载 composer.phar"
    wget https://github.com/composer/composer/releases/latest/download/composer.phar -O composer.phar
fi
