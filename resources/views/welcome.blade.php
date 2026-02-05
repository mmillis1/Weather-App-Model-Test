<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Weather Studio') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,600,700&family=manrope:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-[#f7f3ee] text-[#161410] antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_#fff7ec_0%,_#f7f3ee_48%,_#efe7dc_100%)]"></div>
            <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(35,24,16,0.08)_1px,transparent_1px),linear-gradient(to_bottom,rgba(35,24,16,0.08)_1px,transparent_1px)] bg-[size:48px_48px] opacity-40"></div>
            <div class="pointer-events-none absolute left-1/2 top-0 h-[28rem] w-[44rem] -translate-x-1/2 bg-[radial-gradient(circle,_rgba(255,214,170,0.35),transparent_65%)]"></div>

            <main class="relative mx-auto flex min-h-screen max-w-5xl flex-col items-center justify-center px-6 py-16">
                <div class="w-full max-w-3xl text-center animate-[rise_0.9s_ease-out] motion-reduce:animate-none">
                    <p class="text-xs uppercase tracking-[0.48em] text-[#857c6b]">Weather atelier</p>
                    <h1 class="mt-6 font-serif text-4xl md:text-6xl tracking-tight text-[#1b1914]">
                        A refined look at your local sky.
                    </h1>
                    <p class="mt-4 text-base md:text-lg text-[#5f584b]">
                        Tap once and we will bring back the current conditions, styled with care.
                    </p>

                    <div class="mt-10 flex flex-col items-center gap-4">
                        <button
                            id="get-weather"
                            type="button"
                            data-endpoint="{{ route('weather.show') }}"
                            class="group relative inline-flex items-center justify-center rounded-full border border-[#1b1a16] bg-[#1b1a16] px-12 py-4 text-lg font-semibold text-[#f7f3ee] shadow-[0_18px_40px_rgba(27,26,22,0.35)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#14130f] hover:shadow-[0_24px_50px_rgba(27,26,22,0.4)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#c08a5a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#f7f3ee] disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            <span class="absolute inset-0 rounded-full bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.25),transparent_60%)] opacity-0 transition group-hover:opacity-100"></span>
                            <span class="relative inline-flex items-center gap-3">
                                <span class="flex h-2.5 w-2.5 rounded-full bg-[#f0b37e] shadow-[0_0_0_6px_rgba(240,179,126,0.25)] transition group-hover:scale-110"></span>
                                <span>Get my weather</span>
                                <span id="weather-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-[#f2d3b3] border-t-transparent"></span>
                            </span>
                        </button>
                        <p class="text-[11px] uppercase tracking-[0.32em] text-[#857c6b]">Powered by OpenWeather</p>
                    </div>

                    <p id="weather-status" role="status" aria-live="polite" class="mt-4 min-h-5 text-sm text-[#6a6356]"></p>

                    <noscript>
                        <div class="mt-6 rounded-2xl border border-[#e2d6c8] bg-white/80 px-6 py-4 text-sm text-[#6a6356]">
                            JavaScript is required to request your location. Please enable it to fetch your local weather.
                        </div>
                    </noscript>
                </div>

                <section
                    id="weather-card"
                    class="mt-12 hidden w-full max-w-3xl rounded-3xl border border-[#e0d3c4] bg-white/80 p-8 shadow-[0_20px_60px_rgba(41,30,18,0.18)] backdrop-blur-sm motion-reduce:transition-none"
                >
                    <div id="weather-loading" class="hidden">
                        <p class="text-xs uppercase tracking-[0.3em] text-[#857c6b]">Preparing your forecast</p>
                        <div class="mt-6 space-y-4">
                            <div class="h-5 w-36 rounded-full bg-[linear-gradient(110deg,#f1e6da,45%,#fbf6f0,55%,#f1e6da)] bg-[length:200%_100%] animate-[shimmer_1.6s_ease-in-out_infinite]"></div>
                            <div class="h-10 w-64 rounded-2xl bg-[linear-gradient(110deg,#f1e6da,45%,#fbf6f0,55%,#f1e6da)] bg-[length:200%_100%] animate-[shimmer_1.6s_ease-in-out_infinite]"></div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="h-20 rounded-2xl bg-[linear-gradient(110deg,#f1e6da,45%,#fbf6f0,55%,#f1e6da)] bg-[length:200%_100%] animate-[shimmer_1.6s_ease-in-out_infinite]"></div>
                                <div class="h-20 rounded-2xl bg-[linear-gradient(110deg,#f1e6da,45%,#fbf6f0,55%,#f1e6da)] bg-[length:200%_100%] animate-[shimmer_1.6s_ease-in-out_infinite]"></div>
                                <div class="h-20 rounded-2xl bg-[linear-gradient(110deg,#f1e6da,45%,#fbf6f0,55%,#f1e6da)] bg-[length:200%_100%] animate-[shimmer_1.6s_ease-in-out_infinite]"></div>
                            </div>
                        </div>
                    </div>

                    <div id="weather-error" class="hidden text-left">
                        <p class="text-xs uppercase tracking-[0.3em] text-[#857c6b]">Unable to fetch</p>
                        <h2 class="mt-3 font-serif text-2xl text-[#1b1914]">We hit a snag</h2>
                        <p id="weather-error-text" class="mt-2 text-sm text-[#6a6356]"></p>
                        <button
                            id="weather-retry"
                            type="button"
                            class="mt-5 inline-flex items-center justify-center rounded-full border border-[#c8b5a3] px-6 py-2 text-sm font-semibold text-[#4b3f32] transition hover:border-[#a98a72] hover:text-[#2b241d]"
                        >
                            Try again
                        </button>
                    </div>

                    <div id="weather-success" class="hidden text-left">
                        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-[#857c6b]">Current conditions</p>
                                <h2 id="weather-location" class="mt-3 font-serif text-3xl text-[#1b1914]">--</h2>
                                <p id="weather-region" class="mt-2 hidden text-sm text-[#6a6356]"></p>
                                <p id="weather-condition" class="mt-3 text-sm text-[#6a6356]">--</p>
                            </div>
                            <div class="text-left md:text-right">
                                <p id="weather-temp" class="font-serif text-5xl text-[#1b1914]">--</p>
                                <p class="mt-2 text-sm text-[#6a6356]">Feels like <span id="weather-feels">--</span></p>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-[#efe5d9] bg-[#fbf8f4] p-4">
                                <p class="text-[11px] uppercase tracking-[0.3em] text-[#857c6b]">Wind</p>
                                <p id="weather-wind" class="mt-3 text-lg font-semibold text-[#1b1914]">--</p>
                            </div>
                            <div class="rounded-2xl border border-[#efe5d9] bg-[#fbf8f4] p-4">
                                <p class="text-[11px] uppercase tracking-[0.3em] text-[#857c6b]">Humidity</p>
                                <p id="weather-humidity" class="mt-3 text-lg font-semibold text-[#1b1914]">--</p>
                            </div>
                            <div class="rounded-2xl border border-[#efe5d9] bg-[#fbf8f4] p-4">
                                <p class="text-[11px] uppercase tracking-[0.3em] text-[#857c6b]">Updated</p>
                                <p id="weather-updated" class="mt-3 text-lg font-semibold text-[#1b1914]">--</p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
