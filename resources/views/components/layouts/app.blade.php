<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    <x-theme-script />

    @vite('resources/js/theme-init.js')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <wireui:scripts />
    <!-- TODO 用laravel推荐的方式安装这个包 -->
    <script src="//unpkg.com/alpinejs" defer></script>

</head>

<body>

    <x-header />

    {{ $slot }}

    <x-footer />

</body>

</html>