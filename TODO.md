TODO
在配置应用程序的索引设置之后，你必须调用 scout:sync-index-settings Artisan 命令。此命令将向 Meilisearch 通知你当前配置的索引设置。为了方便起见，你可能希望将此命令作为部署过程的一部分：

php artisan scout:sync-index-settings

---

TODO
如果你使用的是 Laravel Scout 的默认配置，那么你可能希望在部署过程中运行 php artisan scout:import 命令。此命令将遍历你的数据库并导入所有记录到 Meilisearch。

---

TODO
在部署过程中需要运行 schedule:clear-cache Artisan 命令，否则任务会被死锁卡住。

在幕后，withoutOverlapping 方法使用应用程序的 cache 来获取锁。如果必要，你可以使用 schedule:clear-cache Artisan 命令清除这些缓存锁。通常只有在服务器出现意外问题导致任务卡住时才需要这样做。