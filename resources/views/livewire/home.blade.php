<div>
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex">
            <h1 class="text-3xl tracking-tight text-gray-900">标题</h1>
            <div class="text-right flex-1">
                @auth
                    <x-button flat label="进入控制台" href="" />
                @else
                    <x-button flat label="登录" href="" />
                    <x-button label="注册" href="" />
                @endauth
            </div>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="text-4xl text-center my-8">
                副标题
            </div>
            <div class="text-2xl text-gray-600 text-center my-8">
                介绍...
            </div>

            <div class="text-center">
                <x-button class="mx-3" xl positive label="立即选购" href="" />
                <x-button xl warning label="控制台" href="" />
            </div>
        
        </div>
    </main>

</div>