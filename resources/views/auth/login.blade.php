<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="app-base" content="{{ rtrim(url('/'), '/') }}">

        <title>ログイン — {{ config('app.name', 'Daily Management') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div
            id="app"
            data-page="login"
            data-app-name="{{ config('app.name', 'Daily Management') }}"
            data-login-url="{{ route('login') }}"
            data-dashboard-url="{{ route('dashboard') }}"
        ></div>
    </body>
</html>
