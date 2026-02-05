@extends('layouts.app')

@section('content')
    {{-- Animated gradient orbs --}}
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    {{-- Dark mode toggle --}}
    <button id="theme-toggle" type="button" class="theme-toggle" aria-label="Toggle dark mode">
        <svg id="icon-sun" class="hidden size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
        </svg>
        <svg id="icon-moon" class="hidden size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
        </svg>
    </button>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            {{-- Hero section --}}
            <div id="hero-section" class="text-center">
                <h1 class="app-title mb-2 text-5xl font-bold tracking-tight">Nimbus</h1>
                <p class="mb-8 text-base text-slate-500 dark:text-slate-400">
                    Because looking outside is overrated.
                </p>
                <button id="btn-get-weather" type="button" class="btn-gradient">
                    <span id="btn-text">Get My Weather</span>
                    <svg id="btn-spinner" class="spinner hidden size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>

            {{-- Weather card --}}
            <div id="weather-card" class="glass-card mt-8 hidden p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 id="weather-location" class="text-xl font-semibold text-slate-800 dark:text-white"></h2>
                        <p id="weather-time" class="text-sm text-slate-500 dark:text-slate-400"></p>
                    </div>
                    <img id="weather-icon" class="size-16" src="" alt="Weather icon">
                </div>

                <div class="mb-4 text-center">
                    <span id="weather-temp" class="temp-large"></span>
                    <p id="weather-desc" class="text-lg text-slate-600 dark:text-slate-300"></p>
                </div>

                <div class="weather-grid">
                    <div class="weather-item">
                        <span class="weather-item-label">Feels Like</span>
                        <span id="weather-feels" class="weather-item-value"></span>
                    </div>
                    <div class="weather-item">
                        <span class="weather-item-label">Wind</span>
                        <span id="weather-wind" class="weather-item-value"></span>
                    </div>
                    <div class="weather-item">
                        <span class="weather-item-label">Humidity</span>
                        <span id="weather-humidity" class="weather-item-value"></span>
                    </div>
                    <div class="weather-item">
                        <span class="weather-item-label">Condition</span>
                        <span id="weather-condition" class="weather-item-value"></span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <button id="btn-refresh" type="button" class="btn-gradient text-sm">
                        Check Again
                    </button>
                </div>
            </div>

            {{-- Error card --}}
            <div id="error-card" class="error-card mt-8 hidden p-6 text-center">
                <p id="error-message" class="mb-4 text-red-700 dark:text-red-300"></p>
                <button id="btn-retry" type="button" class="btn-gradient text-sm">
                    Try Again
                </button>
            </div>
        </div>
    </main>

    <noscript>
        <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
            <div class="rounded-xl bg-white p-8 text-center shadow-lg">
                <h1 class="mb-2 text-2xl font-bold text-slate-800">Nimbus</h1>
                <p class="text-slate-600">JavaScript is required to use this weather app.</p>
            </div>
        </div>
    </noscript>
@endsection
