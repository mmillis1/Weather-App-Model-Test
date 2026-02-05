import './bootstrap';

const states = {
    initial: document.getElementById('state-initial'),
    loading: document.getElementById('state-loading'),
    weather: document.getElementById('state-weather'),
    error: document.getElementById('state-error'),
};

function showState(name) {
    Object.entries(states).forEach(([key, el]) => {
        el.classList.toggle('hidden', key !== name);
    });
}

function showError(message) {
    document.getElementById('error-message').textContent = message;
    showState('error');
}

function populateWeather(data) {
    document.getElementById('weather-city').textContent = data.city;
    document.getElementById('weather-temp').textContent = data.temperature;
    document.getElementById('weather-feels-like').textContent = data.feels_like;
    document.getElementById('weather-description').textContent = data.description;
    document.getElementById('weather-humidity').textContent = data.humidity;
    document.getElementById('weather-wind').textContent = data.wind_speed;
    document.getElementById('weather-updated').textContent = `Updated ${data.updated_at}`;

    const icon = document.getElementById('weather-icon');
    icon.src = `https://openweathermap.org/img/wn/${data.icon}@2x.png`;
    icon.alt = data.description;

    showState('weather');
}

function fetchWeather() {
    if (!navigator.geolocation) {
        showError('Geolocation is not supported by your browser.');
        return;
    }

    showState('loading');

    navigator.geolocation.getCurrentPosition(
        (position) => {
            window.axios.post('/weather', {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
            })
            .then((response) => populateWeather(response.data.data))
            .catch((error) => {
                const message = error.response?.data?.message
                    || 'Something went wrong fetching the weather.';
                showError(message);
            });
        },
        (error) => {
            const messages = {
                [error.PERMISSION_DENIED]: 'Location permission denied. Please allow location access and try again.',
                [error.POSITION_UNAVAILABLE]: 'Location information is unavailable. Please try again.',
                [error.TIMEOUT]: 'The location request timed out. Please try again.',
            };
            showError(messages[error.code] || 'An unknown error occurred getting your location.');
        },
        { timeout: 10000 }
    );
}

document.getElementById('btn-get-weather').addEventListener('click', fetchWeather);
document.getElementById('btn-refresh').addEventListener('click', fetchWeather);
document.getElementById('btn-try-again').addEventListener('click', fetchWeather);
