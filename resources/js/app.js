import './bootstrap';

// ---- Theme Management ----
function initTheme() {
    const isDark = localStorage.theme === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    document.documentElement.classList.toggle('dark', isDark);
    updateThemeIcons(isDark);
}

function updateThemeIcons(isDark) {
    const sunIcon = document.getElementById('icon-sun');
    const moonIcon = document.getElementById('icon-moon');

    if (sunIcon && moonIcon) {
        sunIcon.classList.toggle('hidden', !isDark);
        moonIcon.classList.toggle('hidden', isDark);
    }
}

function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.theme = isDark ? 'dark' : 'light';
    updateThemeIcons(isDark);
}

// ---- Weather API ----
async function fetchWeather(lat, lon) {
    try {
        const response = await window.axios.get('/api/weather', {
            params: { lat, lon },
        });
        return { success: true, data: response.data.data };
    } catch (error) {
        const message = error.response?.data?.error
            || error.response?.data?.message
            || 'Something went wrong. Please try again.';
        return { success: false, error: message };
    }
}

// ---- UI Updates ----
function updateWeatherUI(data) {
    document.getElementById('weather-location').textContent = `${data.location}, ${data.region}`;
    document.getElementById('weather-time').textContent = `Updated at ${data.updated_at}`;
    document.getElementById('weather-icon').src = `https://openweathermap.org/img/wn/${data.icon}@2x.png`;
    document.getElementById('weather-icon').alt = data.description;
    document.getElementById('weather-temp').textContent = `${data.temperature}\u00B0F`;
    document.getElementById('weather-desc').textContent = data.description;
    document.getElementById('weather-feels').textContent = `${data.feels_like}\u00B0F`;
    document.getElementById('weather-wind').textContent = `${data.wind_speed} mph`;
    document.getElementById('weather-humidity').textContent = `${data.humidity}%`;
    document.getElementById('weather-condition').textContent = data.condition;

    document.getElementById('weather-card').classList.remove('hidden');
    document.getElementById('error-card').classList.add('hidden');
}

function showError(message) {
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-card').classList.remove('hidden');
    document.getElementById('weather-card').classList.add('hidden');
}

function setLoading(isLoading) {
    const btn = document.getElementById('btn-get-weather');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');

    btn.disabled = isLoading;
    btnText.textContent = isLoading ? 'Reading the skies...' : 'Get My Weather';
    btnSpinner.classList.toggle('hidden', !isLoading);
}

// ---- Geolocation + Fetch ----
async function getWeather() {
    if (!navigator.geolocation) {
        showError('Geolocation is not supported by your browser.');
        return;
    }

    setLoading(true);

    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const result = await fetchWeather(position.coords.latitude, position.coords.longitude);

            if (result.success) {
                updateWeatherUI(result.data);
            } else {
                showError(result.error);
            }

            setLoading(false);
        },
        (error) => {
            setLoading(false);

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    showError('Location access was denied. Please enable location permissions and try again.');
                    break;
                case error.POSITION_UNAVAILABLE:
                    showError('Location information is unavailable. Please try again.');
                    break;
                case error.TIMEOUT:
                    showError('Location request timed out. Please try again.');
                    break;
                default:
                    showError('An unknown error occurred while getting your location.');
            }
        },
        { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 },
    );
}

// ---- Event Listeners ----
document.addEventListener('DOMContentLoaded', () => {
    initTheme();

    document.getElementById('theme-toggle')?.addEventListener('click', toggleTheme);
    document.getElementById('btn-get-weather')?.addEventListener('click', getWeather);
    document.getElementById('btn-refresh')?.addEventListener('click', getWeather);
    document.getElementById('btn-retry')?.addEventListener('click', getWeather);
});
