<x-filament::page>
    <x-filament::card>
        <h2 class="text-lg font-medium">Memory Usage</h2>
        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-4">
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">Used Memory</div>
                <div class="mt-1 text-xl font-semibold">{{ $memoryUsage['used'] ?? 'N/A' }}</div>
            </div>
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">Free Memory</div>
                <div class="mt-1 text-xl font-semibold">{{ $memoryUsage['free'] ?? 'N/A' }}</div>
            </div>
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">Wasted Memory</div>
                <div class="mt-1 text-xl font-semibold">{{ $memoryUsage['wasted'] ?? 'N/A' }}</div>
            </div>
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">Wasted Percentage</div>
                <div class="mt-1 text-xl font-semibold">{{ $memoryUsage['current_wasted_percentage'] ?? 'N/A' }}</div>
            </div>
        </div>
    </x-filament::card>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-medium">OpCache Status</h2>
        <div class="mt-4 overflow-x-auto">
            {!! $status !!}
        </div>
    </x-filament::card>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-medium">OpCache Configuration</h2>
        <div class="mt-4 overflow-x-auto">
            {!! $config !!}
        </div>
    </x-filament::card>
</x-filament::page>