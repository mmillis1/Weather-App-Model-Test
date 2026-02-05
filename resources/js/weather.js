const weatherEmojis = {
    '01d': '\u2600\uFE0F',     // clear sky day
    '01n': '\uD83C\uDF11',     // clear sky night
    '02d': '\u26C5',           // few clouds day
    '02n': '\uD83C\uDF19',     // few clouds night
    '03d': '\u2601\uFE0F',     // scattered clouds
    '03n': '\u2601\uFE0F',
    '04d': '\uD83C\uDF25\uFE0F', // broken clouds
    '04n': '\uD83C\uDF25\uFE0F',
    '09d': '\uD83C\uDF27\uFE0F', // shower rain
    '09n': '\uD83C\uDF27\uFE0F',
    '10d': '\uD83C\uDF26\uFE0F', // rain day
    '10n': '\uD83C\uDF27\uFE0F', // rain night
    '11d': '\u26C8\uFE0F',     // thunderstorm
    '11n': '\u26C8\uFE0F',
    '13d': '\u2744\uFE0F',     // snow
    '13n': '\u2744\uFE0F',
    '50d': '\uD83C\uDF2B\uFE0F', // mist
    '50n': '\uD83C\uDF2B\uFE0F',
};

const states = ['state-initial', 'state-loading', 'state-error', 'state-weather'];

function showState(id) {
    states.forEach(s => {
        document.getElementById(s).classList.toggle('hidden', s !== id);
    });
}

function setLoadingText(text) {
    document.getElementById('loading-text').textContent = text;
}

function showError(icon, title, message) {
    document.getElementById('error-icon').innerHTML = icon;
    document.getElementById('error-title').textContent = title;
    document.getElementById('error-message').textContent = message;
    showState('state-error');
}

function renderWeatherCard(data) {
    document.getElementById('weather-city').textContent = `${data.city}, ${data.country}`;
    document.getElementById('weather-emoji').textContent = weatherEmojis[data.icon] || '\uD83C\uDF24\uFE0F';
    document.getElementById('weather-temp').textContent = `${data.temperature}\u00B0F`;
    document.getElementById('weather-feels').textContent = `Feels like ${data.feels_like}\u00B0F`;
    document.getElementById('weather-condition').textContent = data.condition_description;
    document.getElementById('weather-wind').textContent = `${data.wind_speed} mph`;
    document.getElementById('weather-humidity').textContent = `${data.humidity}%`;
    document.getElementById('weather-updated').textContent = data.updated_at;

    // Reset animation
    const card = document.querySelector('#state-weather > div');
    card.classList.remove('animate-slide-up');
    void card.offsetWidth;
    card.classList.add('animate-slide-up');

    showState('state-weather');
}

async function fetchWeather(latitude, longitude) {
    setLoadingText('Fetching weather...');

    try {
        const url = new URL(window.weatherConfig.url, window.location.origin);
        url.searchParams.set('latitude', latitude);
        url.searchParams.set('longitude', longitude);

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.status === 422) {
            showError('\uD83D\uDCCD', 'Invalid Location', 'The location coordinates appear to be invalid.');
            return;
        }

        if (!response.ok) {
            const body = await response.json().catch(() => ({}));
            showError(
                '\u26C8\uFE0F',
                'Weather Unavailable',
                body.message || 'Weather data is currently unavailable. Please try again later.'
            );
            return;
        }

        const json = await response.json();

        if (!json.success) {
            showError('\u26C8\uFE0F', 'Weather Unavailable', json.message || 'Something went wrong.');
            return;
        }

        renderWeatherCard(json.data);
    } catch {
        showError('\uD83D\uDD0C', 'Connection Error', 'Unable to connect. Please check your internet and try again.');
    }
}

function getLocation() {
    if (!navigator.geolocation) {
        showError('\uD83C\uDF10', 'Not Supported', 'Your browser does not support geolocation.');
        return;
    }

    showState('state-loading');
    setLoadingText('Locating you...');

    navigator.geolocation.getCurrentPosition(
        (position) => {
            fetchWeather(position.coords.latitude, position.coords.longitude);
        },
        (error) => {
            const errors = {
                [GeolocationPositionError.PERMISSION_DENIED]: {
                    icon: '\uD83D\uDD12',
                    title: 'Permission Denied',
                    message: 'Please allow location access in your browser settings and try again.',
                },
                [GeolocationPositionError.POSITION_UNAVAILABLE]: {
                    icon: '\uD83D\uDCE1',
                    title: 'Location Unavailable',
                    message: 'Unable to determine your location. Please try again.',
                },
                [GeolocationPositionError.TIMEOUT]: {
                    icon: '\u23F3',
                    title: 'Request Timed Out',
                    message: 'Location request took too long. Please try again.',
                },
            };

            const info = errors[error.code] || {
                icon: '\u26A0\uFE0F',
                title: 'Location Error',
                message: 'An unknown error occurred while getting your location.',
            };

            showError(info.icon, info.title, info.message);
        },
        {
            enableHighAccuracy: false,
            timeout: 10000,
            maximumAge: 300000,
        }
    );
}

document.getElementById('btn-get-weather').addEventListener('click', getLocation);
document.getElementById('btn-try-again').addEventListener('click', getLocation);
document.getElementById('btn-refresh').addEventListener('click', getLocation);
