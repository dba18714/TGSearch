<x-filament-panels::page>

<div class="flex flex-col p-4">
    <h1 class="text-2xl font-bold">购买页面</h1>
    <p class="mt-2">欢迎来到购买页面！在这里，你可以查看和购买套餐。</p>

    {{-- 示例按钮 --}}
    <x-filament::button color="primary" class="mt-4">购买套餐</x-filament::button>

    {{-- 示例表单 --}}
    {{-- <x-filament class="mt-6"> --}}
        <x-filament::input name="email" label="电子邮件" type="email" required />
        <x-filament::button>提交</x-filament::button>
    {{-- </x-filament> --}}
</div>

</x-filament-panels::page>
