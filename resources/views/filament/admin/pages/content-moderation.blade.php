<x-filament::page>
    <form>
        {{ $this->form }}

        <div class="my-4 flex justify-center">
            <x-filament::actions :actions="$this->getFormActions()" />
        </div>
    </form>

    @if ($result)
        <div class="mt-8">
            <div class="rounded-lg bg-white shadow p-4 dark:bg-gray-800">
                <h3 class="text-lg font-medium mb-4">检测结果</h3>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <span class="font-medium mr-2">状态:</span>
                        @if ($result['safe'])
                            <span class="text-green-600 dark:text-green-400">安全</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">存在问题</span>
                        @endif
                    </div>

                    @if (!$result['safe'] && !empty($result['issues']))
                        <div>
                            <span class="font-medium">发现的问题:</span>
                            <ul class="mt-2 space-y-2">
                                @foreach ($result['issues'] as $issue)
                                    <li class="flex items-center">
                                        <span class="text-red-600 dark:text-red-400">
                                            {{ $issue['category'] }}
                                        </span>
                                        <span class="ml-2 text-gray-600 dark:text-gray-400">
                                            (评分: {{ number_format($issue['score'], 3) }})
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
</x-filament::page>
