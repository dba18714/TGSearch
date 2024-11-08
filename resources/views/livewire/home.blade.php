<div>
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex">
            <h1 class="text-3xl tracking-tight text-gray-900">冲上云霄</h1>
            <div class="text-right flex-1">
                @auth
                    <x-button flat label="进入控制台" :href="" />
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
                冲上云霄，畅游自由的网络世界
            </div>
            <div class="text-2xl text-gray-600 text-center my-8">
                欢迎来到冲上云霄，这里提供安全、可靠、实惠的网络加速服务，让你畅游互联网，突破地域限制，尽享全球信息！
            </div>

            <div class="text-center">
                <x-button class="mx-3" xl positive label="立即选购" href="" />
                <x-button xl warning label="控制台" href="" />
            </div>
        
        </div>
    </main>

</div>