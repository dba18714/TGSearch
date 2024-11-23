<x-filament-panels::page>
    <form wire:submit="check">
        {{ $this->form }}

        <div class="my-4 flex justify-center">
            <x-filament::actions :actions="$this->getFormActions()" />
        </div>
    </form>

    @if($result)
        <div class="mt-4">
            <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                <div class="p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                        检测结果
                    </h3>

                    <div class="mt-4">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">状态：</span>
                            @if($result['safe'])
                                <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/20">
                                    安全
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/20">
                                    存在问题
                                </span>
                            @endif
                        </div>

                        @if(!empty($result['issues']))
                            <div class="mt-4">
                                <span class="font-medium">发现的问题：</span>
                                <ul class="mt-2 list-disc list-inside">
                                    @foreach($result['issues'] as $issue)
                                        <li>
                                            {{ [
                                                'Porn' => '色情',
                                                'Abuse' => '辱骂',
                                                'Ad' => '广告',
                                                'Illegal' => '违法',
                                                'Spam' => '垃圾信息'
                                            ][$issue['category']] ?? $issue['category'] }}
                                            @if(isset($issue['score']))
                                                (置信度: {{ number_format($issue['score'], 1) }}%)
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>