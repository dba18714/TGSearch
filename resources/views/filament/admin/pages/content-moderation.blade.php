<x-filament-panels::page>
    {{ $this->form }}

    <div class="mt-4 flex justify-center">
        <x-filament::actions :actions="$this->getFormActions()" />
    </div>

    @if ($isPassed !== null)
        <div class="mt-6" id="result-section">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">内容审核结果</h3>
                    @if ($isPassed)
                        <span
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400 dark:ring-1 dark:ring-green-500/50">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            安全内容
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400 dark:ring-1 dark:ring-red-500/50">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            存在风险
                        </span>
                    @endif
                </div>

                <div class="mt-6 space-y-6">
                    <div
                        class="bg-gray-50/50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">最严重的问题
                        </h4>
                        <div class="mt-3">
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-yellow-50 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 dark:ring-1 dark:ring-yellow-500/50">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                {{ $maxRisk['category'] }}
                                <span class="ml-1.5 text-yellow-700 dark:text-yellow-400">(置信度:
                                    {{ number_format($maxRisk['score'] * 100, 1) }}%)</span>
                            </span>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50/50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            所有检测到的问题</h4>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($risks as $risk)
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-yellow-50 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 dark:ring-1 dark:ring-yellow-500/50">
                                    {{ $risk['category'] }}
                                    <span
                                        class="ml-1.5 text-yellow-700 dark:text-yellow-400">({{ number_format($risk['score'] * 100, 1) }}%)</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('content-checked', () => {
                // 等待DOM更新完成后再滚动
                setTimeout(() => {
                    const resultSection = document.getElementById('result-section');
                    if (resultSection) {
                        resultSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }, 100);
            });
        });
    </script>
</x-filament-panels::page>
