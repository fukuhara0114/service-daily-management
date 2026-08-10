<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="app-base" content="{{ rtrim(url('/'), '/') }}">

        <title>日次記録 — {{ config('app.name', 'Daily Management') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[var(--color-surface)] text-[var(--color-ink)] antialiased">
        <div class="mx-auto min-h-screen max-w-6xl px-4 py-8 sm:px-6">
            <div class="mb-4 flex justify-end">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg border border-[var(--color-line)] bg-white px-4 py-2 text-sm font-medium text-[var(--color-ink)] transition hover:bg-[var(--color-surface)]"
                    >
                        ログアウト
                    </button>
                </form>
            </div>

            <div
                id="app"
                data-page="dashboard"
                data-app-name="{{ config('app.name', 'Daily Management') }}"
                data-user-name="{{ $user->name }}"
                data-activities-url="{{ $activitiesUrl }}"
                data-upsert-url="{{ $upsertUrl }}"
            ></div>
        </div>
    </body>
</html>
