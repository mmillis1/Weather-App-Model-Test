import './bootstrap';

// Weather App functionality
document.addEventListener('DOMContentLoaded', () => {
    const getWeatherBtn = document.getElementById('get-weather-btn');
    const buttonText = document.getElementById('button-text');
    const loadingSpinner = document.getElementById('loading-spinner');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const weatherCardContainer = document.getElementById('weather-card-container');

    if (!getWeatherBtn) return;

    getWeatherBtn.addEventListener('click', async () => {
        // Reset UI
        errorMessage.classList.add('hidden');
        weatherCardContainer.classList.add('hidden');
        weatherCardContainer.innerHTML = '';

        // Check geolocation support
        if (!navigator.geolocation) {
            showError("Your browser doesn't support location services. Please use a modern browser.");
            return;
        }

        // Show loading state
        setLoading(true);

        try {
            // Get user location
            const position = await getUserLocation();
            const { latitude, longitude } = position.coords;

            // Fetch weather data
            const weatherData = await fetchWeather(latitude, longitude);

            // Display weather card
            displayWeatherCard(weatherData);
        } catch (error) {
            handleError(error);
        } finally {
            setLoading(false);
        }
    });

    function getUserLocation() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, (error) => {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        reject(new Error('Please allow location access to get weather information.'));
                        break;
                    case error.POSITION_UNAVAILABLE:
                        reject(new Error('Unable to determine your location. Please try again.'));
                        break;
                    case error.TIMEOUT:
                        reject(new Error('Location request timed out. Please try again.'));
                        break;
                    default:
                        reject(new Error('An unknown error occurred while getting your location.'));
                }
            }, {
                timeout: 10000,
                enableHighAccuracy: false
            });
        });
    }

    async function fetchWeather(lat, lon) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch('/weather', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ lat, lon })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Unable to fetch weather data. Please try again.');
        }

        if (!data.success) {
            throw new Error(data.message || 'Weather service error. Please try again.');
        }

        return data.data;
    }

    function displayWeatherCard(data) {
        const card = `
            <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6 shadow-lg transform transition-all duration-500"
                 style="animation: slideIn 0.5s ease-out forwards;">
                <!-- Location -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                        ${data.city}${data.country ? `, ${data.country}` : ''}
                    </h2>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1 capitalize">${data.description}</p>
                </div>

                <!-- Temperature Display -->
                <div class="text-center mb-6">
                    <div class="text-6xl font-bold text-[#f53003] dark:text-[#FF4433] mb-2">
                        ${data.temperature}°C
                    </div>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">
                        Feels like ${data.feels_like}°C
                    </p>
                </div>

                <!-- Weather Details Grid -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="text-center">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Condition</p>
                        <p class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${data.condition}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Humidity</p>
                        <p class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${data.humidity}%</p>
                    </div>
                    <div class="text-center col-span-2">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Wind Speed</p>
                        <p class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${data.wind_speed} m/s</p>
                    </div>
                </div>

                <!-- Timestamp -->
                <div class="text-center mt-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                        Just now
                    </p>
                </div>
            </div>
        `;

        weatherCardContainer.innerHTML = card;
        weatherCardContainer.classList.remove('hidden');
    }

    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
    }

    function handleError(error) {
        console.error('Weather app error:', error);
        showError(error.message || 'An unexpected error occurred. Please try again.');
    }

    function setLoading(isLoading) {
        getWeatherBtn.disabled = isLoading;

        if (isLoading) {
            buttonText.textContent = 'Loading...';
            loadingSpinner.classList.remove('hidden');
        } else {
            buttonText.textContent = 'Get My Weather';
            loadingSpinner.classList.add('hidden');
        }
    }
});

// Add animation styles
if (!document.getElementById('weather-animations')) {
    const style = document.createElement('style');
    style.id = 'weather-animations';
    style.textContent = `
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(1rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
}
