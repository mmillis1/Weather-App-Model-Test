<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Weather App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 flex items-center justify-center p-4">
    <noscript>
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">JavaScript Required</h1>
            <p class="text-gray-600">This weather application requires JavaScript to function. Please enable JavaScript in your browser settings and refresh the page.</p>
        </div>
    </noscript>

    <div id="app" class="w-full max-w-md">
        <div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl p-8 text-white">
            <h1 class="text-4xl font-bold text-center mb-2">Weather</h1>
            <p class="text-white/80 text-center mb-8">Get current weather for your location</p>

            <button id="getWeatherBtn" class="w-full bg-white text-blue-600 font-semibold py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-lg">
                Get my weather
            </button>

            <div id="loading" class="hidden mt-6 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                <p class="mt-2 text-white/80">Fetching weather...</p>
            </div>

            <div id="errorMessage" class="hidden mt-6 bg-red-500/20 border border-red-500/50 rounded-xl p-4 text-center">
                <p id="errorText"></p>
                <button id="retryBtn" class="mt-3 bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition-colors">
                    Try again
                </button>
            </div>

            <div id="weatherCard" class="hidden mt-6">
                <div class="bg-white/20 rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 id="city" class="text-2xl font-bold"></h2>
                            <p id="region" class="text-white/80"></p>
                        </div>
                        <div class="text-right">
                            <p id="temp" class="text-5xl font-bold"></p>
                            <p id="condition" class="text-white/80 capitalize"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/20">
                        <div class="text-center">
                            <p class="text-white/60 text-xs uppercase tracking-wide">Feels Like</p>
                            <p id="feelsLike" class="text-xl font-semibold mt-1"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-white/60 text-xs uppercase tracking-wide">Wind</p>
                            <p id="wind" class="text-xl font-semibold mt-1"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-white/60 text-xs uppercase tracking-wide">Humidity</p>
                            <p id="humidity" class="text-xl font-semibold mt-1"></p>
                        </div>
                    </div>

                    <p id="updatedAt" class="mt-4 text-center text-white/60 text-sm"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const getWeatherBtn = document.getElementById('getWeatherBtn');
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            const retryBtn = document.getElementById('retryBtn');
            const weatherCard = document.getElementById('weatherCard');

            function showError(message) {
                errorText.textContent = message;
                errorMessage.classList.remove('hidden');
            }

            function hideError() {
                errorMessage.classList.add('hidden');
            }

            function showLoading() {
                loading.classList.remove('hidden');
                getWeatherBtn.disabled = true;
                getWeatherBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            function hideLoading() {
                loading.classList.add('hidden');
                getWeatherBtn.disabled = false;
                getWeatherBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            function showWeatherCard() {
                weatherCard.classList.remove('hidden');
            }

            function hideWeatherCard() {
                weatherCard.classList.add('hidden');
            }

            function displayWeather(data) {
                document.getElementById('city').textContent = data.city || 'Unknown';
                document.getElementById('region').textContent = data.region || '';
                document.getElementById('temp').textContent = Math.round(data.temp) + '°C';
                document.getElementById('condition').textContent = data.description || data.condition || '';
                document.getElementById('feelsLike').textContent = Math.round(data.feels_like) + '°C';
                document.getElementById('wind').textContent = data.wind + ' m/s';
                document.getElementById('humidity').textContent = data.humidity + '%';
                document.getElementById('updatedAt').textContent = 'Updated at ' + data.updated_at;
            }

            async function getWeather(lat, lon) {
                showLoading();
                hideError();
                hideWeatherCard();

                try {
                    const response = await fetch('/api/weather', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify({ lat, lon })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to fetch weather data');
                    }

                    displayWeather(data);
                    showWeatherCard();
                } catch (error) {
                    showError(error.message || 'Failed to fetch weather data. Please try again.');
                } finally {
                    hideLoading();
                }
            }

            function handleGetWeather() {
                if (!navigator.geolocation) {
                    showError('Geolocation is not supported by your browser');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        getWeather(position.coords.latitude, position.coords.longitude);
                    },
                    (error) => {
                        let message = 'Failed to get your location';

                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                message = 'Location access was denied. Please enable location permissions in your browser settings.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                message = 'Location information is unavailable. Please try again.';
                                break;
                            case error.TIMEOUT:
                                message = 'Location request timed out. Please try again.';
                                break;
                        }

                        showError(message);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            getWeatherBtn.addEventListener('click', handleGetWeather);
            retryBtn.addEventListener('click', handleGetWeather);
        });
    </script>
</body>
</html>