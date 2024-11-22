<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {!! SEO::generate() !!}

    <x-theme-init-script />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

    <x-header />

    {{ $slot }}

    <x-footer />

</body>

</html>