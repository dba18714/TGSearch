# 生产环境部署 

## 使用`aaPanel`部署

### 建议的服务器版本
Ubuntu22.04, minimum 1 core 2G RAM

*如果是 1G RAM 会导致 PHP 扩展: fileinfo 安装时卡死*
*请尽量使用以上版本，其他版本可能会有未知问题。*

### 首次部署

- cloudflare：
  - 配置域名解析A记录指向VPS IP地址
  - SSL/TLS > 配置 > 加密模式 > 自定义 SSL/TLS > 选择`完全` > 保存。（否则访问域名会死循环重定向）
- 安装`aaPanel`面板
- `aaPanel`官网：https://www.aapanel.com
- `aaPanel`安装完成后，访问面板，会弹出环境安装选项，选择：
  - Nginx1.24+ 
  - PHP8.2 (apparel的php8.3没有找到`pdo_pgsql`这个扩展)
  - 正式生产环境选择编译安装，测试环境选择 Fast 快速编译安装。
<!-- - 应用商店 > 搜素`sql`，安装`PostgreSQL Manager`。
- `PostgreSQL Manager`安装完成后，打开`PostgreSQL Manager`设定窗口 > `Version management` > PgSQL Version: 选择 16.1+，点击安装。
- 创建数据库：打开`PostgreSQL Manager`设定窗口 > DB List > Add DB：
  - DB Name 填: tgsearch
  - Username 填: tgsearch
  - Password 随机
  - 提交。 -->
- 安装扩展：App Store > 找到对应的PHP版本点击 Setting > Install extentions > 安装以下扩展：
  - `pdo_pgsql`(必须，aapanel的php8.3没有找到这个扩展, aapanel官方说8.3还没适配，所以只能使用8.2)
  - `redis`
  - `fileinfo`
  - `exif`
  - `mbstring`(aapanel的php8.2没有找到这个扩展, 无安装未知是否有影响)
  - `opcache`(安装opcache后对网站加载速度有显著的提升)
- 解除被禁止的函数：App Store > 找到对应的PHP版本点击 Setting > Disabled functions 将以下从列表中删除：
  - `putenv`
  - `proc_open`
  - `symlink`
  - `shell_exec`
- 新建站点 > 填写域名，选择PHP版本，确定。
- Site > 设置 SSL 并开启 Force HTTPS  
- Site directory -> 关闭（否则无法删除根目录的所有文件）：防止 XSS 攻擊  
- Site > 根目录 > 删除根目录下的所有文件  
- 待所有队列都执行成功后，重启VPS，`pdo_pgsql`扩展才会生效，单单重载/重启php-fpm不会生效。
- 修改本地项目目录的 ./deploy.env 配置项，SERVER_PATH 为上面步骤所创建的站点的根目录，其他配置项根据实际情况填写。  
- 创建数据库网络：aapanel > Docker > 安装Docker，安装后点击网络选项卡 > 添加网络：
  - 網絡名稱：yisou_net
  - 设备：bridge
  - IPv4子網：172.19.0.0/16
  - IPv4網關：172.19.0.1
  - IPv4範圍：172.19.0.0/24
  - 点击确定。
- 创建数据库：aapanel > Docker > 安装Docker，安装后点击容器编排选项卡 > 添加容器编排：
  - 名称填：pgsql
  - 组成填：
``` yml
services:
    pgsql:
        image: 'postgres:17-alpine'
        ports:
            - '5432:5432'
        restart: always
        environment:
            POSTGRES_DB: '${DB_DATABASE}'
            POSTGRES_USER: '${DB_USERNAME}'
            POSTGRES_PASSWORD: '${DB_PASSWORD:-secret}'
        volumes:
            - 'dbdata:/var/lib/postgresql/data'
        networks:
            - yisou_net
        healthcheck:
            test:
                - CMD
                - pg_isready
                - '-q'
                - '-d'
                - '${DB_DATABASE}'
                - '-U'
                - '${DB_USERNAME}'
            retries: 3
            timeout: 5s
volumes:
    dbdata:
        driver: local
networks:
  yisou_net:
    external: true
```
  - .env內容填 ./deploy.env 里的：
``` env
DB_DATABASE="tgsearch"
DB_USERNAME="tgsearch"
DB_PASSWORD="2hjtLb3d4KZsKP2f"
```
  - 点击`确定`编排。
- 在本地项目根目录执行 ./deploy.sh
- 配置伪静态（URL rewrite）选择“zblog”或使用以下⬇️规则并保存。
  - （注意：如果选的是“laravel5”会导致filament后台CSS样式文件404） 
  - （注意：v2board官方文档所提供的“URL rewrite”也会导致filament后台CSS样式文件404） 
    - 目前观察到会404的文件路径：https://x.xx/livewire/livewire.min.js?id=02b08710
  ``` nginx
  if (!-f $request_filename){
    rewrite (.*) /index.php;
  }
  ```
- 配置运行目录：Site directory -> Running directory 选择“/public”并保存
- 现在可以访问站点了。
- 导入数据库备份（如果需要）：资料库 > 导入 > 上传备份文件 > 点击导入
- 配置定时任务  
> aaPanel 面板 > Cron。  
>
> 在 Type of Task 选择 Shell Script  
> 在 Name of Task 填写 TGSearch  
> 在 Period 选择 N Minutes 1 Minute  
> 在 Run User 选择 www  
> 在 Script content 填写 php /www/wwwroot/路径/artisan schedule:run  
>
> 根据上述信息添加每1分钟执行一次的定时任务。
- 启动队列服务
> TGSearch 的系统强依赖队列服务，正常使用 TGSearch 必须启动队列服务。    
> 下面以 aaPanel 中的 supervisor 服务来守护队列服务作为演示。  
>
> aaPanel 面板 > App Store > Tools  
>
> 找到 Supervisor 进行安装，安装完成后点击设置 > Add Daemon 按照如下填  
>  
>
> 在 Name 填写 TGSearch  
> 在 Run User 选择 www  
> 在 Run Dir 选择 站点目录  
> 在 Start Command 填写 php artisan horizon  
> 在 Processes 填写 1  
>
> 填写后点击 Confirm 添加即可运行。  
- 部署完成。

### 系统更新

- 在本地项目根目录执行 ./deploy.sh
- 更新完成。

### 其他信息

如果手动修改了生产环境下的源代码，需要执行`php artisan optimize:clear`后才会生效

[宝塔面板/aaPanel如何切换默认的命令行php版本](https://www.bt.cn/bbs/forum.php?mod=redirect&goto=findpost&ptid=22467&pid=483577)

### Git 安装：
```shell
$ yum install curl-devel expat-devel gettext-devel \
  openssl-devel zlib-devel

$ yum -y install git-core

$ git --version
git version 1.7.1
```

启用 http2（ nutgram 的默认配置需要开启 http2 ）: https://www.aapanel.com/forum/d/21948-how-to-enable-http2-in-php82/4


---
* 未整理：

## 开发时常用的命令

运行 Horizon
php artisan horizon

你可以使用 Artisan 命令 horizon:pause 和 horizon:continue 来暂停和指示任务继续运行：

php artisan horizon:pause

php artisan horizon:continue


你可以使用 Artisan 命令 horizon:terminate 优雅地终止机器上的主 Horizon 进程。Horizon 会等当前正在处理的所有任务都完成后退出：

php artisan horizon:terminate


使用 Horizon 时，应使用 horizon:clear 命令从队列中清除作业，而不是使用 queue:clear 命令。

sail artisan horizon:clear

sail artisan scout:sync-index-settings

sail artisan scout:sync-index-settings && sail php artisan scout:flush "App\Models\UnifiedSearch" && sail artisan migrate:fresh --seed

## 启动长轮训
sail artisan nutgram:run
Or
sail artisan app:telegram:run --timeout=30 # TODO 超时时间没有生效

## 测试 Telegram bot 
sail artisan telegram:test

## 注册 bot 命令
sail artisan nutgram:register-commands

## 部署到服务器
修改 deploy.sh 配置中的域名和路径
给脚本添加执行权限：chmod +x deploy.sh
运行部署脚本: ./deploy.sh



本地 macOS 数据库测试：

mysql 8.0
select count(*) as aggregate from `chats` 两百万行	901.82ms	

mysql 5.7
select count(*) as aggregate from `chats` 两百万行	365.69ms

pgsql 15
select count(*) as aggregate from "chats" 两百万行	45.52ms

pgsql 16
select count(*) as aggregate from "chats" 两百万行	43.74ms	

pgsql 17.2
select count(*) as aggregate from "chats" 两百万行	45.30ms	

