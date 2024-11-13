<div>
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">数据统计</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="text-center">
                <span class="block text-2xl font-bold text-green-600 dark:text-green-400" 
                      x-data="{ 
                          current: 0,
                          target: {{ $totalChannels }},
                          init() {
                              let start = 0;
                              const duration = 2000;
                              const stepTime = 50;
                              const steps = duration / stepTime;
                              const increment = this.target / steps;
                              const timer = setInterval(() => {
                                  start += increment;
                                  if (start >= this.target) {
                                      clearInterval(timer);
                                      start = this.target;
                                  }
                                  this.current = Math.floor(start);
                              }, stepTime);
                          }
                      }"
                      x-text="current.toLocaleString()">0</span>
                <span class="text-sm text-gray-600 dark:text-gray-400">频道</span>
            </div>
            <div class="text-center">
                <span class="block text-2xl font-bold text-yellow-600 dark:text-yellow-400" 
                      x-data="{ 
                          current: 0,
                          target: {{ $totalGroups }},
                          init() {
                              let start = 0;
                              const duration = 2000;
                              const stepTime = 50;
                              const steps = duration / stepTime;
                              const increment = this.target / steps;
                              const timer = setInterval(() => {
                                  start += increment;
                                  if (start >= this.target) {
                                      clearInterval(timer);
                                      start = this.target;
                                  }
                                  this.current = Math.floor(start);
                              }, stepTime);
                          }
                      }"
                      x-text="current.toLocaleString()">0</span>
                <span class="text-sm text-gray-600 dark:text-gray-400">群组</span>
            </div>
            <div class="text-center">
                <span class="block text-2xl font-bold text-purple-600 dark:text-purple-400" 
                      x-data="{ 
                          current: 0,
                          target: {{ $totalBots }},
                          init() {
                              let start = 0;
                              const duration = 2000;
                              const stepTime = 50;
                              const steps = duration / stepTime;
                              const increment = this.target / steps;
                              const timer = setInterval(() => {
                                  start += increment;
                                  if (start >= this.target) {
                                      clearInterval(timer);
                                      start = this.target;
                                  }
                                  this.current = Math.floor(start);
                              }, stepTime);
                          }
                      }"
                      x-text="current.toLocaleString()">0</span>
                <span class="text-sm text-gray-600 dark:text-gray-400">机器人</span>
            </div>
            <div class="text-center">
                <span class="block text-2xl font-bold text-indigo-600 dark:text-indigo-400" 
                      x-data="{ 
                          current: 0,
                          target: {{ $totalPersons }},
                          init() {
                              let start = 0;
                              const duration = 2000;
                              const stepTime = 50;
                              const steps = duration / stepTime;
                              const increment = this.target / steps;
                              const timer = setInterval(() => {
                                  start += increment;
                                  if (start >= this.target) {
                                      clearInterval(timer);
                                      start = this.target;
                                  }
                                  this.current = Math.floor(start);
                              }, stepTime);
                          }
                      }"
                      x-text="current.toLocaleString()">0</span>
                <span class="text-sm text-gray-600 dark:text-gray-400">个人</span>
            </div>
            <div class="text-center">
                <span class="block text-2xl font-bold text-red-600 dark:text-red-400" 
                      x-data="{ 
                          current: 0,
                          target: {{ $totalMessages }},
                          init() {
                              let start = 0;
                              const duration = 2000;
                              const stepTime = 50;
                              const steps = duration / stepTime;
                              const increment = this.target / steps;
                              const timer = setInterval(() => {
                                  start += increment;
                                  if (start >= this.target) {
                                      clearInterval(timer);
                                      start = this.target;
                                  }
                                  this.current = Math.floor(start);
                              }, stepTime);
                          }
                      }"
                      x-text="current.toLocaleString()">0</span>
                <span class="text-sm text-gray-600 dark:text-gray-400">消息</span>
            </div>
        </div>
        <div class="mt-6 pt-4">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4">
                <div class="text-center">
                    <div class="flex items-center justify-center mt-1">
                        <span class="mr-2 text-sm text-gray-600 dark:text-gray-400">当前已收录</span>
                        <span class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent"
                              x-data="{ 
                                  current: 0,
                                  target: {{ $totalLinks }},
                                  init() {
                                      let start = 0;
                                      const duration = 2000;
                                      const stepTime = 50;
                                      const steps = duration / stepTime;
                                      const increment = this.target / steps;
                                      const timer = setInterval(() => {
                                          start += increment;
                                          if (start >= this.target) {
                                              clearInterval(timer);
                                              start = this.target;
                                          }
                                          this.current = Math.floor(start);
                                      }, stepTime);
                                  }
                              }"
                              x-text="current.toLocaleString()">
                            0
                        </span>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">条记录</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- 新增的更新频率说明 -->
        <div class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
            <i class="fas fa-sync-alt mr-1"></i> 统计数据每小时更新一次
        </div>
    </div>
</div>