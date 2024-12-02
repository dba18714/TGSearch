#!/bin/bash

if ! command -v node &> /dev/null; then
    # 添加 NodeSource 仓库
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    apt-get install -y nodejs
fi
