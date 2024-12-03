<x-filament::page>
    <x-filament::card>
        <form wire:submit="submit">
            {{ $this->form }}
            
            {{-- # TODO 解决所有filament自定义视图无法使用（不会自动更新）tailwindcss的问题 --}}
            <div class="mt-4 flex space-x-4">
                <x-filament::button
                wire:click="submit"
                >
                    设置 Webhook
                </x-filament::button>

                <x-filament::button
                    wire:click="getWebhookInfo"
                    color="info"
                    type="button"
                >
                    获取 Webhook 信息
                </x-filament::button>

                <x-filament::button
                    wire:click="deleteWebhook"
                    color="danger"
                    type="button"
                >
                    删除 Webhook
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament::page>