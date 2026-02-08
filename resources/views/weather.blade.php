<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Weather App</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gradient-to-br from-sky-100 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <!-- Main Card -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-blue-500/10 dark:shadow-slate-900/50 border border-white/50 dark:border-slate-700/50 overflow-hidden">
                
                <!-- Header -->
                <div class="px-8 pt-8 pb-4">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                        </svg>
                        <h1 class="text-xl font-semibold text-slate-700 dark:text-slate-200">Weather</h1>
                    </div>
                    <p class="text-center text-slate-500 dark:text-slate-400 text-sm">Discover the weather in your location</p>
                </div>

                <!-- Content Area -->
                <div class="px-8 pb-8">
                    
                    <!-- Initial State - Get Weather Button -->
                    <div id="initial-state" class="text-center py-8">
                        <button 
                            id="get-weather-btn"
                            class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white transition-all duration-300 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 hover:scale-[1.02] hover:-translate-y-0.5 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800"
                        >
                            <span class="absolute inset-0 rounded-2xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                            <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Get my weather
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-state" class="hidden text-center py-12">
                        <div class="relative inline-flex">
                            <div class="w-12 h-12 rounded-full border-4 border-blue-200 dark:border-blue-900/50"></div>
                            <div class="absolute top-0 left-0 w-12 h-12 rounded-full border-4 border-blue-500 border-t-transparent animate-spin"></div>
                        </div>
                        <p class="mt-4 text-slate-500 dark:text-slate-400 animate-pulse">Getting your location...</p>
                    </div>

                    <!-- Weather Card -->
                    <div id="weather-card" class="hidden">
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 p-6 text-white shadow-xl">
                            <!-- Decorative Elements -->
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
                            
                            <!-- Location -->
                            <div class="relative">
                                <h2 id="location-name" class="text-2xl font-bold tracking-tight"></h2>
                                <p id="updated-time" class="text-blue-100 text-sm mt-1"></p>
                            </div>

                            <!-- Main Weather Display -->
                            <div class="flex items-center justify-between mt-6">
                                <div>
                                    <div class="flex items-start">
                                        <span id="temperature" class="text-6xl font-light tracking-tighter"></span>
                                        <span class="text-3xl font-light mt-1">°</span>
                                    </div>
                                    <p id="condition" class="text-lg text-blue-100 mt-1 capitalize"></p>
                                </div>
                                <div id="weather-icon" class="text-6xl">
                                    <!-- Icon will be inserted here -->
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/20">
                                <div class="text-center">
                                    <svg class="w-5 h-5 mx-auto mb-1 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-xs text-blue-200">Feels Like</p>
                                    <p id="feels-like" class="font-semibold"></p>
                                </div>
                                <div class="text-center">
                                    <svg class="w-5 h-5 mx-auto mb-1 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-xs text-blue-200">Wind</p>
                                    <p id="wind-speed" class="font-semibold"></p>
                                </div>
                                <div class="text-center">
                                    <svg class="w-5 h-5 mx-auto mb-1 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                    <p class="text-xs text-blue-200">Humidity</p>
                                    <p id="humidity" class="font-semibold"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Refresh Button -->
                        <button 
                            id="refresh-btn"
                            class="w-full mt-4 py-3 px-4 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Check Again
                        </button>
                    </div>

                    <!-- Error State -->
                    <div id="error-state" class="hidden text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                            <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 id="error-title" class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">Something went wrong</h3>
                        <p id="error-message" class="text-slate-500 dark:text-slate-400 text-sm mb-4"></p>
                        <button 
                            id="retry-btn"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200"
                        >
                            Try Again
                        </button>
                    </div>

                    <!-- No JavaScript Fallback -->
                    <noscript>
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 mb-4">
                                <svg class="w-8 h-8 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">JavaScript Required</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">This weather app requires JavaScript to detect your location. Please enable JavaScript in your browser settings to use this feature.</p>
                        </div>
                    </noscript>

                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-slate-400 dark:text-slate-500 text-xs mt-6">
                Powered by OpenWeatherMap
            </p>
        </div>

        <script>
            (function() {
                const getWeatherBtn = document.getElementById('get-weather-btn');
                const refreshBtn = document.getElementById('refresh-btn');
                const retryBtn = document.getElementById('retry-btn');
                const initialState = document.getElementById('initial-state');
                const loadingState = document.getElementById('loading-state');
                const weatherCard = document.getElementById('weather-card');
                const errorState = document.getElementById('error-state');

                // Weather icon mapping
                const weatherIcons = {
                    '01d': '☀️', '01n': '🌙',
                    '02d': '⛅', '02n': '☁️',
                    '03d': '☁️', '03n': '☁️',
                    '04d': '☁️', '04n': '☁️',
                    '09d': '🌧️', '09n': '🌧️',
                    '10d': '🌦️', '10n': '🌧️',
                    '11d': '⛈️', '11n': '⛈️',
                    '13d': '❄️', '13n': '❄️',
                    '50d': '🌫️', '50n': '🌫️'
                };

                function showState(state) {
                    initialState.classList.add('hidden');
                    loadingState.classList.add('hidden');
                    weatherCard.classList.add('hidden');
                    errorState.classList.add('hidden');
                    state.classList.remove('hidden');
                }

                function showError(title, message) {
                    document.getElementById('error-title').textContent = title;
                    document.getElementById('error-message').textContent = message;
                    showState(errorState);
                }

                function displayWeather(data) {
                    document.getElementById('location-name').textContent = data.city + (data.country ? `, ${data.country}` : '');
                    document.getElementById('updated-time').textContent = `Updated at ${data.updated_at}`;
                    document.getElementById('temperature').textContent = data.temperature;
                    document.getElementById('condition').textContent = data.description || data.condition;
                    document.getElementById('feels-like').textContent = `${data.feels_like}°`;
                    document.getElementById('wind-speed').textContent = `${data.wind_speed} m/s`;
                    document.getElementById('humidity').textContent = `${data.humidity}%`;
                    document.getElementById('weather-icon').textContent = weatherIcons[data.icon] || '🌡️';
                    showState(weatherCard);
                }

                async function fetchWeather(lat, lon) {
                    showState(loadingState);
                    
                    try {
                        const response = await fetch('/api/weather', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ lat, lon })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.error || 'Failed to fetch weather data');
                        }

                        displayWeather(data);
                    } catch (error) {
                        showError('Unable to get weather', error.message || 'Something went wrong. Please try again.');
                    }
                }

                function getLocation() {
                    if (!navigator.geolocation) {
                        showError('Location Not Supported', 'Your browser does not support geolocation. Please use a modern browser to access this feature.');
                        return;
                    }

                    showState(loadingState);

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            fetchWeather(position.coords.latitude, position.coords.longitude);
                        },
                        (error) => {
                            let message = 'Unable to retrieve your location.';
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    message = 'Location access was denied. Please enable location permissions in your browser settings to use this feature.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    message = 'Location information is unavailable. Please try again later.';
                                    break;
                                case error.TIMEOUT:
                                    message = 'The request to get your location timed out. Please try again.';
                                    break;
                            }
                            showError('Location Error', message);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                }

                getWeatherBtn.addEventListener('click', getLocation);
                refreshBtn.addEventListener('click', getLocation);
                retryBtn.addEventListener('click', getLocation);
            })();
        </script>
    </body>
</html>
