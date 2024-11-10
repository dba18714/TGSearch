TODO
在配置应用程序的索引设置之后，你必须调用 scout:sync-index-settings Artisan 命令。此命令将向 Meilisearch 通知你当前配置的索引设置。为了方便起见，你可能希望将此命令作为部署过程的一部分：

php artisan scout:sync-index-settings

---