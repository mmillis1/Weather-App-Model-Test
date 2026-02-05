import './bootstrap';

const htmlElement = document.documentElement;
const weatherButton = document.getElementById('get-weather-button');
const themeButton = document.getElementById('theme-toggle-button');
const statusPanel = document.getElementById('weather-status');
const errorPanel = document.getElementById('weather-error');
const weatherCard = document.getElementById('weather-card');

const locationLabelElement = document.getElementById('weather-location-label');
const temperatureElement = document.getElementById('weather-temperature');
const feelsLikeElement = document.getElementById('weather-feels-like');
const conditionElement = document.getElementById('weather-condition');
const windElement = document.getElementById('weather-wind');
const humidityElement = document.getElementById('weather-humidity');
const updatedAtElement = document.getElementById('weather-updated-at');

const metricRegions = new Set(['US', 'LR', 'MM']);

let isDarkMode = false;

const setTheme = (darkModeEnabled) => {
    isDarkMode = darkModeEnabled;
    htmlElement.classList.toggle('dark', darkModeEnabled);
    themeButton?.setAttribute('aria-pressed', darkModeEnabled ? 'true' : 'false');

    if (themeButton) {
        themeButton.textContent = darkModeEnabled ? 'Switch to light mode' : 'Switch to dark mode';
    }
};

const setStatus = (message) => {
    if (statusPanel) {
        statusPanel.textContent = message;
    }
};

const setError = (message) => {
    if (! errorPanel) {
        return;
    }

    errorPanel.textContent = message;
    errorPanel.classList.remove('hidden');
};

const clearError = () => {
    if (! errorPanel) {
        return;
    }

    errorPanel.textContent = '';
    errorPanel.classList.add('hidden');
};

const hideWeatherCard = () => {
    weatherCard?.classList.add('hidden');
};

const showWeatherCard = () => {
    weatherCard?.classList.remove('hidden');
};

const detectUnits = () => {
    const locale = navigator.language ?? 'en-US';
    const region = locale.includes('-') ? locale.split('-')[1]?.toUpperCase() : 'US';

    return metricRegions.has(region ?? 'US') ? 'imperial' : 'metric';
};

const geolocate = () => {
    return new Promise((resolve, reject) => {
        if (! navigator.geolocation) {
            reject({ code: 'unsupported' });

            return;
        }

        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                resolve({ latitude: coords.latitude, longitude: coords.longitude });
            },
            (error) => {
                reject(error);
            },
            {
                timeout: 10000,
                enableHighAccuracy: true,
                maximumAge: 60000,
            },
        );
    });
};

const formatUpdatedAt = (isoDate) => {
    if (! isoDate) {
        return 'Just now';
    }

    const date = new Date(isoDate);

    return new Intl.DateTimeFormat(undefined, {
        hour: 'numeric',
        minute: '2-digit',
        month: 'short',
        day: 'numeric',
    }).format(date);
};

const renderWeather = (payload) => {
    const { data } = payload;
    const temperatureUnit = data.weather.units === 'imperial' ? 'F' : 'C';
    const windUnit = data.weather.units === 'imperial' ? 'mph' : 'm/s';

    locationLabelElement.textContent = data.location.label ?? 'Your current location';
    temperatureElement.textContent = data.weather.temperature !== null
        ? `${Math.round(data.weather.temperature)}°${temperatureUnit}`
        : '--';
    feelsLikeElement.textContent = data.weather.feels_like !== null
        ? `${Math.round(data.weather.feels_like)}°${temperatureUnit}`
        : '--';
    conditionElement.textContent = data.weather.condition;
    windElement.textContent = data.weather.wind_speed !== null
        ? `${data.weather.wind_speed} ${windUnit}`
        : '--';
    humidityElement.textContent = data.weather.humidity !== null
        ? `${data.weather.humidity}%`
        : '--';
    updatedAtElement.textContent = formatUpdatedAt(data.updated_at);

    showWeatherCard();
};

const getFriendlyGeolocationError = (error) => {
    if (error.code === 'unsupported') {
        return 'Your browser does not support geolocation yet. Try a modern browser to unlock weather magic.';
    }

    if (error.code === 1) {
        return 'Location access was denied. Tempest needs your location to bring your local sky to life.';
    }

    if (error.code === 2) {
        return 'We could not determine your location. Try again in a moment.';
    }

    if (error.code === 3) {
        return 'Location lookup timed out. Please try one more time.';
    }

    return 'Something unexpected happened while reading your location.';
};

const getFriendlyApiError = async (response) => {
    if (response.status === 422) {
        return 'Those coordinates did not validate. Please try again.';
    }

    if (response.status === 429) {
        return 'Our weather source is taking a quick breather. Try again shortly.';
    }

    if (response.status === 502 || response.status === 503) {
        return 'The weather service is stormy right now. Give us another tap in a moment.';
    }

    const payload = await response.json().catch(() => null);

    return payload?.error?.message ?? 'We could not fetch weather right now.';
};

const setButtonLoading = (loading) => {
    if (! weatherButton) {
        return;
    }

    weatherButton.disabled = loading;
    weatherButton.classList.toggle('opacity-80', loading);
    weatherButton.textContent = loading ? 'Summoning your sky...' : 'Get my weather';
};

const fetchWeather = async () => {
    clearError();
    hideWeatherCard();
    setStatus('Checking your location...');
    setButtonLoading(true);

    try {
        const position = await geolocate();
        setStatus('Gathering your forecast...');

        const response = await fetch('/api/weather/current', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                latitude: position.latitude,
                longitude: position.longitude,
                units: detectUnits(),
            }),
        });

        if (! response.ok) {
            const message = await getFriendlyApiError(response);
            throw new Error(message);
        }

        const payload = await response.json();
        renderWeather(payload);
        setStatus('Forecast refreshed. Looking sharp out there.');
    } catch (error) {
        const isGeolocationError =
            (typeof GeolocationPositionError !== 'undefined' && error instanceof GeolocationPositionError)
            || (typeof error === 'object' && error !== null && 'code' in error);

        if (isGeolocationError) {
            setError(getFriendlyGeolocationError(error));
        } else if (error instanceof TypeError) {
            setError('Network trouble in the atmosphere. Please check your connection and try again.');
        } else {
            setError(error.message ?? 'Something went wrong. Please try again.');
        }

        setStatus('No weather yet. We are ready when you are.');
    } finally {
        setButtonLoading(false);
    }
};

setTheme(false);

themeButton?.addEventListener('click', () => {
    setTheme(! isDarkMode);
});

weatherButton?.addEventListener('click', () => {
    fetchWeather();
});
