#!/bin/bash

echo "🚀 开始部署..."

# 定义颜色输出
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 加载 deploy.env 文件
if [ ! -f deploy.env ]; then
    echo "❌ deploy.env 文件不存在！"
    exit 1
fi
export $(grep -v '^#' deploy.env | xargs)

# 生成 SSH 密钥： 在你的本地机器上（macOS），运行以下命令生成 SSH 密钥对：
KEY_PATH="$HOME/.ssh/id_rsa"

# 检查密钥是否已经存在
if [ -f "$KEY_PATH" ]; then
  echo "SSH 密钥已存在，跳过生成步骤。"
else
  # 生成新的 SSH 密钥
  echo "SSH 密钥不存在，开始生成..."
  ssh-keygen -t rsa -b 4096 -f "$KEY_PATH" -N ""  # -N "" 表示没有密码短语
  echo "SSH 密钥生成完毕。"
fi

# 检查是否存在主机密钥冲突
echo "检查是否存在主机密钥冲突..."
ssh-keygen -R $SERVER_HOST &>/dev/null  # 删除旧的主机密钥（如果存在）
ssh-keyscan -H $SERVER_HOST >> ~/.ssh/known_hosts 2>/dev/null  # 添加新的主机密钥
if [ $? -eq 0 ]; then
    echo "✅ 主机密钥已更新。"
else
    echo "❌ 无法更新主机密钥，请检查网络或服务器配置。"
    exit 1
fi

# 检查是否可以无密码登录（即公钥是否已经上传）
if ssh -o PasswordAuthentication=no -o BatchMode=yes $SERVER_USER@$SERVER_HOST exit &>/dev/null; then
    echo "SSH 公钥已经上传，跳过上传步骤。"
else
    # 将公钥复制到服务器： 使用 ssh-copy-id 命令将公钥上传到服务器：
    # 这会要求输入你服务器的密码，然后它会将公钥添加到服务器的 ~/.ssh/authorized_keys 文件中。下次无需再输入你服务器的密码。
    echo "上传 SSH 公钥到服务器..."
    ssh-copy-id $SERVER_USER@$SERVER_HOST
fi

# 使用 scp 或 rsync 将文件上传到服务器
echo "开始上传文件到服务器..."
rsync -avz --files-from=<(git ls-files) --rsync-path="mkdir -p $SERVER_PATH && rsync" ./ $SERVER_USER@$SERVER_HOST:$SERVER_PATH

# 检查上传是否成功
if [ $? -eq 0 ]; then
    echo "✅ 文件上传成功！"
else
    echo "❌ 文件上传失败！"
    exit 1
fi

# 使用 SSH 连接到服务器并执行部署步骤
ssh -t $SERVER_USER@$SERVER_HOST "bash -c '
    set -e
    cd $SERVER_PATH

    # # 检查是否已存在 .env 文件
    # if [ -f .env ]; then
    #     echo "❌ 服务器的 $SERVER_PATH/.env 文件已存在，如需重新安装请删除目录下.env文件，删除前请先备份！"
    #     exit 1
    # fi

    source _deploy/install.sh
'"

if [ $? -ne 0 ]; then
    echo "❌ 部署失败！退出码: $DEPLOY_STATUS"
    exit 1
fi


echo "✅ 脚本执行完毕."
