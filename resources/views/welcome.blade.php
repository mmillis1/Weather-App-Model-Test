<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Weather') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gradient-to-br from-sky-50 via-blue-50 to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 min-h-screen flex items-center justify-center p-4">

        {{-- Initial State: Get Weather Button --}}
        <div id="state-initial" class="text-center">
            <div class="mb-8">
                <svg class="mx-auto w-16 h-16 text-sky-500 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
                </svg>
                <h1 class="mt-4 text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Weather</h1>
                <p class="mt-2 text-slate-500 dark:text-slate-400">Get the current weather for your location</p>
            </div>
            <button
                id="btn-get-weather"
                type="button"
                class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 transition hover:bg-sky-600 hover:shadow-sky-600/30 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 dark:focus:ring-offset-slate-900 cursor-pointer"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                Get My Weather
            </button>
        </div>

        {{-- Loading State --}}
        <div id="state-loading" class="hidden text-center">
            <svg class="mx-auto w-12 h-12 text-sky-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="mt-4 text-slate-600 dark:text-slate-300 font-medium">Fetching your weather...</p>
        </div>

        {{-- Weather Card --}}
        <div id="state-weather" class="hidden w-full max-w-md">
            <div class="rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl shadow-xl shadow-slate-900/5 dark:shadow-slate-900/30 ring-1 ring-slate-900/5 dark:ring-white/10 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-sky-500 to-blue-600 px-6 py-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 id="weather-city" class="text-xl font-bold"></h2>
                            <p id="weather-updated" class="mt-0.5 text-sm text-sky-100"></p>
                        </div>
                        <img id="weather-icon" src="" alt="" class="w-16 h-16 drop-shadow-md" />
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-5xl font-bold text-slate-900 dark:text-white tracking-tight">
                                <span id="weather-temp"></span><span class="text-2xl text-slate-400 dark:text-slate-500">&deg;F</span>
                            </p>
                            <p id="weather-description" class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400"></p>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Feels like <span id="weather-feels-like" class="font-semibold text-slate-700 dark:text-slate-300"></span>&deg;
                        </p>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 px-4 py-3">
                            <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            </svg>
                            <div>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Humidity</p>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200"><span id="weather-humidity"></span>%</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 px-4 py-3">
                            <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M2.25 21h19.5M12 6.75l-1.5 6h3L12 19.5" />
                            </svg>
                            <div>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Wind</p>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200"><span id="weather-wind"></span> mph</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t border-slate-100 dark:border-slate-700/50 px-6 py-4">
                    <button
                        id="btn-refresh"
                        type="button"
                        class="inline-flex items-center gap-2 text-sm font-medium text-sky-600 dark:text-sky-400 transition hover:text-sky-700 dark:hover:text-sky-300 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.992 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182M2.985 19.644l3.181-3.183" />
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- Error State --}}
        <div id="state-error" class="hidden text-center max-w-sm">
            <div class="rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl shadow-xl ring-1 ring-slate-900/5 dark:ring-white/10 px-6 py-8">
                <svg class="mx-auto w-12 h-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <p id="error-message" class="mt-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed"></p>
                <button
                    id="btn-try-again"
                    type="button"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-slate-900 dark:bg-white px-6 py-2.5 text-sm font-semibold text-white dark:text-slate-900 shadow-sm transition hover:bg-slate-800 dark:hover:bg-slate-100 cursor-pointer"
                >
                    Try Again
                </button>
            </div>
        </div>

        <noscript>
            <div class="fixed inset-0 flex items-center justify-center bg-slate-50 dark:bg-slate-900 p-4">
                <div class="text-center max-w-sm">
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">JavaScript Required</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Please enable JavaScript to use the weather app.</p>
                </div>
            </div>
        </noscript>

    </body>
</html>
