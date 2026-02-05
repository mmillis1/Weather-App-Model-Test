<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scheme-light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Tempest | Beautiful local weather</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:500,700|plus-jakarta-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: "Plus Jakarta Sans", sans-serif; margin: 0; padding: 2rem; background: #fff8ef; color: #111827; }
            </style>
        @endif
    </head>
    <body class="tempest-shell antialiased">
        <div class="tempest-ambient -left-20 -top-24"></div>
        <div class="tempest-ambient -bottom-20 -right-16"></div>

        <main class="relative z-10 mx-auto flex min-h-screen w-full max-w-6xl items-center px-5 py-10 sm:px-8">
            <section class="tempest-glass w-full">
                <div class="flex flex-col gap-8 md:gap-10">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-3">
                            <p class="inline-flex items-center rounded-full border border-slate-900/10 bg-white/60 px-4 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
                                Tempest
                            </p>
                            <h1 class="font-serif text-4xl leading-tight text-balance sm:text-5xl md:text-6xl">
                                Weather, but make it breathtaking.
                            </h1>
                            <p class="max-w-2xl text-sm leading-7 text-slate-700 sm:text-base dark:text-slate-200">
                                Tempest gives you your sky in one elegant tap. No clutter, no chaos, just forecast confidence with style.
                            </p>
                        </div>

                        <button
                            id="theme-toggle-button"
                            type="button"
                            class="shrink-0 rounded-full border border-slate-900/10 bg-white/65 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 transition hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-sky-600 dark:border-white/15 dark:bg-white/10 dark:text-slate-100"
                            aria-pressed="false"
                        >
                            Switch to dark mode
                        </button>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <button id="get-weather-button" type="button" class="tempest-button animate-tempest-float min-h-16 w-full sm:w-auto">
                                Get my weather
                            </button>

                            <p id="weather-status" class="text-sm font-medium text-slate-700 dark:text-slate-200" aria-live="polite">
                                Tap the button and let Tempest read the room.
                            </p>
                        </div>

                        <p id="weather-error" class="hidden rounded-2xl border border-rose-300/70 bg-rose-100/70 px-4 py-3 text-sm font-medium text-rose-900 dark:border-rose-300/35 dark:bg-rose-900/35 dark:text-rose-100" aria-live="polite"></p>

                        <article id="weather-card" class="tempest-glass hidden p-0 shadow-[0_30px_80px_-40px_rgba(8,16,35,0.7)]">
                            <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6 lg:grid-cols-3">
                                <div class="tempest-metric col-span-full flex flex-col gap-1">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">Location</p>
                                    <p id="weather-location-label" class="font-serif text-2xl text-slate-900 dark:text-slate-50">--</p>
                                </div>

                                <div class="tempest-metric">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">Current temp</p>
                                    <p id="weather-temperature" class="mt-2 text-3xl font-semibold">--</p>
                                </div>

                                <div class="tempest-metric">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">Feels like</p>
                                    <p id="weather-feels-like" class="mt-2 text-3xl font-semibold">--</p>
                                </div>

                                <div class="tempest-metric">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">Condition</p>
                                    <p id="weather-condition" class="mt-2 text-2xl font-semibold">--</p>
                                </div>

                                <div class="tempest-metric">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">Wind</p>
                                    <p id="weather-wind" class="mt-2 text-2xl font-semibold">--</p>
                                </div>

                                <div class="tempest-metric">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">Humidity</p>
                                    <p id="weather-humidity" class="mt-2 text-2xl font-semibold">--</p>
                                </div>

                                <div class="tempest-metric">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">Updated</p>
                                    <p id="weather-updated-at" class="mt-2 text-2xl font-semibold">--</p>
                                </div>
                            </div>
                        </article>

                        <noscript>
                            <p class="rounded-2xl border border-amber-300/80 bg-amber-100/70 px-4 py-3 text-sm font-medium text-amber-900">
                                Tempest needs JavaScript to request your location and fetch live weather. Please enable JavaScript and refresh this page.
                            </p>
                        </noscript>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
