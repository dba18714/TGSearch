<x-filament-panels::page>
    {{ $this->form }}

    <div class="mt-4 flex justify-center">
        <x-filament::actions :actions="$this->getFormActions()" />
    </div>

    @if ($isPassed !== null)
        <div class="mt-4">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">检测结果</h3>
                <div class="mt-2">
                    <div class="flex items-center">
                        <span class="mr-2 text-gray-700 dark:text-gray-300">状态:</span>
                        @if ($isPassed)
                            <span
                                class="px-2 py-1 text-sm text-green-800 dark:text-green-300 bg-green-100 dark:bg-green-900/50 rounded-full">
                                安全
                            </span>
                        @else
                            <span
                                class="px-2 py-1 text-sm text-red-800 dark:text-red-300 bg-red-100 dark:bg-red-900/50 rounded-full">
                                存在问题
                            </span>
                        @endif
                    </div>

                    @if (!$isPassed)
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-700 dark:text-gray-300">最严重的问题：</h4>
                            <ul class="mt-2 space-y-2">
                                <li class="flex items-center">
                                    <span
                                        class="px-2 py-1 text-sm text-yellow-800 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/50 rounded">
                                        {{ $maxRisk['category'] }}
                                        (置信度: {{ $maxRisk['score'] * 100 }}%)
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-700 dark:text-gray-300">所有问题：</h4>
                            <ul class="mt-2 space-y-2">
                                @foreach ($risks as $risk)
                                    <li class="flex items-center">
                                        <span
                                            class="px-2 py-1 text-sm text-yellow-800 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/50 rounded">
                                            {{ $risk['category'] }}
                                            (置信度: {{ $risk['score'] * 100 }}%)
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
