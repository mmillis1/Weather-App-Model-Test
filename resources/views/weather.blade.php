<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weather App</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white font-sans antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">

        {{-- Initial State --}}
        <div id="state-initial" class="text-center">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">Weather</h1>
            <p class="mt-3 text-lg text-blue-200/70">Find out what's happening outside</p>
            <button
                id="btn-get-weather"
                type="button"
                class="mt-8 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-8 py-4 text-lg font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500 hover:shadow-blue-500/40 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900 active:scale-95"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                Get My Weather
            </button>
        </div>

        {{-- Loading State --}}
        <div id="state-loading" class="hidden text-center">
            <div class="inline-flex items-center justify-center">
                <svg class="size-12 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            <p id="loading-text" class="mt-4 text-lg text-blue-200/70">Locating you...</p>
        </div>

        {{-- Error State --}}
        <div id="state-error" class="hidden w-full max-w-md">
            <div class="rounded-2xl bg-white/10 p-8 text-center shadow-lg backdrop-blur-sm">
                <div id="error-icon" class="text-5xl">&#x26A0;&#xFE0F;</div>
                <h2 id="error-title" class="mt-4 text-xl font-semibold">Something went wrong</h2>
                <p id="error-message" class="mt-2 text-blue-200/70">Please try again.</p>
                <button
                    id="btn-try-again"
                    type="button"
                    class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-500 hover:shadow-blue-500/40 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900 active:scale-95"
                >
                    Try Again
                </button>
            </div>
        </div>

        {{-- Weather Card --}}
        <div id="state-weather" class="hidden w-full max-w-md">
            <div class="animate-slide-up rounded-2xl bg-white/10 p-8 shadow-lg backdrop-blur-sm">
                <div class="text-center">
                    <p id="weather-city" class="text-lg font-medium text-blue-200/70"></p>
                    <div id="weather-emoji" class="mt-2 text-7xl"></div>
                    <p id="weather-temp" class="mt-2 text-6xl font-bold tracking-tight"></p>
                    <p id="weather-feels" class="mt-1 text-blue-200/70"></p>
                    <p id="weather-condition" class="mt-1 text-lg font-medium"></p>
                </div>
                <div class="mt-8 grid grid-cols-3 gap-4 border-t border-white/10 pt-6">
                    <div class="text-center">
                        <p class="text-sm text-blue-200/50">Wind</p>
                        <p id="weather-wind" class="mt-1 text-lg font-semibold"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-blue-200/50">Humidity</p>
                        <p id="weather-humidity" class="mt-1 text-lg font-semibold"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-blue-200/50">Updated</p>
                        <p id="weather-updated" class="mt-1 text-lg font-semibold"></p>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <button
                        id="btn-refresh"
                        type="button"
                        class="text-sm text-blue-300/60 transition hover:text-blue-200"
                    >
                        Refresh
                    </button>
                </div>
            </div>
        </div>

    </div>

    <noscript>
        <div class="flex min-h-screen items-center justify-center bg-slate-900 px-4">
            <div class="rounded-2xl bg-white/10 p-8 text-center text-white">
                <p class="text-5xl">&#x1F30D;</p>
                <h2 class="mt-4 text-xl font-semibold">JavaScript Required</h2>
                <p class="mt-2 text-blue-200/70">Please enable JavaScript to use the Weather App.</p>
            </div>
        </div>
    </noscript>

    <script>
        window.weatherConfig = {
            url: "{{ route('weather.show') }}"
        };
    </script>
</body>
</html>
