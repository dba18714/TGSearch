<x-filament-panels::page>
    <x-filament::card>
        <form wire:submit="submit">
            {{ $this->form }}
            
            <div class="mt-6 flex space-x-4">
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
</x-filament-panels::page>