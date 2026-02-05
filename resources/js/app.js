import './bootstrap';

const button = document.getElementById('get-weather');

if (button) {
    const endpoint = button.dataset.endpoint;
    const status = document.getElementById('weather-status');
    const card = document.getElementById('weather-card');
    const loading = document.getElementById('weather-loading');
    const error = document.getElementById('weather-error');
    const errorText = document.getElementById('weather-error-text');
    const success = document.getElementById('weather-success');
    const retry = document.getElementById('weather-retry');
    const spinner = document.getElementById('weather-spinner');

    const locationEl = document.getElementById('weather-location');
    const regionEl = document.getElementById('weather-region');
    const conditionEl = document.getElementById('weather-condition');
    const tempEl = document.getElementById('weather-temp');
    const feelsEl = document.getElementById('weather-feels');
    const windEl = document.getElementById('weather-wind');
    const humidityEl = document.getElementById('weather-humidity');
    const updatedEl = document.getElementById('weather-updated');

    const sections = [loading, error, success];

    const setStatus = (message) => {
        if (status) {
            status.textContent = message ?? '';
        }
    };

    const setButtonLoading = (isLoading) => {
        button.disabled = isLoading;

        if (spinner) {
            spinner.classList.toggle('hidden', !isLoading);
        }
    };

    const showSection = (section) => {
        if (card) {
            card.classList.remove('hidden');
            card.classList.remove('animate-[rise_0.8s_ease-out]');
            void card.offsetWidth;
            card.classList.add('animate-[rise_0.8s_ease-out]');
        }

        sections.forEach((item) => {
            if (item) {
                item.classList.toggle('hidden', item !== section);
            }
        });
    };

    const showError = (message) => {
        if (errorText) {
            errorText.textContent = message;
        }

        setStatus('');
        setButtonLoading(false);
        showSection(error);
    };

    const showLoading = (message) => {
        setStatus(message);
        setButtonLoading(true);
        showSection(loading);
    };

    const showSuccess = (payload) => {
        const locationName = payload?.location?.name || 'Your location';
        const region = payload?.location?.region;
        const condition = payload?.weather?.condition || 'Current conditions';
        const temperature = Number.isFinite(payload?.weather?.temp) ? Math.round(payload.weather.temp) : null;
        const feelsLike = Number.isFinite(payload?.weather?.feels_like)
            ? Math.round(payload.weather.feels_like)
            : null;
        const wind = Number.isFinite(payload?.weather?.wind_mph) ? Math.round(payload.weather.wind_mph) : null;
        const humidity = Number.isFinite(payload?.weather?.humidity) ? Math.round(payload.weather.humidity) : null;
        const tempUnit = payload?.units?.temperature || 'F';
        const windUnit = payload?.units?.wind || 'mph';

        if (locationEl) {
            locationEl.textContent = locationName;
        }

        if (regionEl) {
            if (region) {
                regionEl.textContent = region;
                regionEl.classList.remove('hidden');
            } else {
                regionEl.classList.add('hidden');
            }
        }

        if (conditionEl) {
            conditionEl.textContent = condition;
        }

        if (tempEl) {
            tempEl.textContent = temperature !== null ? `${temperature}°${tempUnit}` : '--';
        }

        if (feelsEl) {
            feelsEl.textContent = feelsLike !== null ? `${feelsLike}°${tempUnit}` : '--';
        }

        if (windEl) {
            windEl.textContent = wind !== null ? `${wind} ${windUnit}` : '--';
        }

        if (humidityEl) {
            humidityEl.textContent = humidity !== null ? `${humidity}%` : '--';
        }

        if (updatedEl) {
            const updatedAt = payload?.updated_at ? new Date(payload.updated_at) : new Date();
            const formatted = new Intl.DateTimeFormat(undefined, {
                hour: 'numeric',
                minute: '2-digit',
            }).format(updatedAt);
            updatedEl.textContent = formatted;
        }

        setStatus('');
        setButtonLoading(false);
        showSection(success);
    };

    const fallbackMessage = (statusCode) => {
        if (statusCode === 422) {
            return 'We could not understand that location. Please try again.';
        }

        if (statusCode === 429) {
            return 'Weather service is busy. Please try again soon.';
        }

        if (statusCode >= 500) {
            return 'Weather service is temporarily unavailable. Please try again.';
        }

        return 'We could not fetch your weather right now. Please try again.';
    };

    const fetchWeather = async (latitude, longitude) => {
        try {
            const response = await fetch(
                `${endpoint}?lat=${encodeURIComponent(latitude)}&lon=${encodeURIComponent(longitude)}`,
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                },
            );

            const payload = await response.json().catch(() => null);

            if (!response.ok) {
                const message = payload?.message || fallbackMessage(response.status);
                showError(message);
                return;
            }

            showSuccess(payload);
        } catch (error) {
            showError('We could not reach the weather service. Please try again.');
        }
    };

    const handleGeoError = (geoError) => {
        if (geoError?.code === geoError.PERMISSION_DENIED) {
            showError('Location access was denied. Please allow it to get your weather.');
            return;
        }

        if (geoError?.code === geoError.POSITION_UNAVAILABLE) {
            showError('We could not determine your location. Please try again.');
            return;
        }

        if (geoError?.code === geoError.TIMEOUT) {
            showError('Location request timed out. Please try again.');
            return;
        }

        showError('We could not access your location. Please try again.');
    };

    const requestWeather = () => {
        if (!endpoint) {
            showError('Weather endpoint is not available.');
            return;
        }

        if (!navigator.geolocation) {
            showError('Your browser does not support location services.');
            return;
        }

        showLoading('Locating you...');

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                showLoading('Fetching the latest conditions...');
                void fetchWeather(latitude, longitude);
            },
            handleGeoError,
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000,
            },
        );
    };

    button.addEventListener('click', requestWeather);

    if (retry) {
        retry.addEventListener('click', requestWeather);
    }
}
