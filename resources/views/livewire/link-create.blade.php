<div class="min-h-screen bg-gray-100 dark:bg-gray-900 pt-8 pb-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">批量添加链接</h2>
            
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="submit">
                <div class="space-y-4">
                    <div>
                        <label for="urls" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            输入链接（每行一个）
                        </label>
                        <textarea
                            id="urls"
                            wire:model="urls"
                            rows="10"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="https://t.me/example&#10;@example&#10;example"
                        ></textarea>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            支持多种格式：完整URL、用户名(@格式)、纯用户名
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            提交
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>