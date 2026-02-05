# Weather App

## I want to build a beautiful, elegant, web application using laravel. The app is intentionally simple, but looks amazing. Make sure to use the brainstorming skill, frontend-design skill and ask me lots of questions.

### This weather app, needs to be gorgeous, breathtaking, and have ALL the visuals, animations, colors, and some basic verbiage, telling users why they need to use OUR weather app. Don't be too verbose, but be witty. Again, I want this website to WOW me in terms of its visuals and css. Don't forget mobile styling!! I want a button that can toggle between light mode and dark mode. Light mode should be default, and can change to dark mode. Make sure to consider color theme and choices to ensure light and dark mode look gorgeous and works well.

The only page is the home page. It has a single large, beautiful button: “Get my weather”

#### When the button is clicked:

- The browser requests the user’s location (Geolocation API).
- The app calls a Laravel endpoint with {lat, lon}.
- Laravel fetches weather from a public weather API and returns a clean JSON response.
- The UI shows a polished “weather card” with:
    - City/region name (if available)
    - Current temp
    - Feels like
    - Condition (ex: Clear / Clouds)
    - Wind
    - Humidity
    - Updated time

## The Constraints/Requirements:

- Use Blade (No SPA).
- Use Tailwind CSS for styling (this is already setup).
- Use vanilla JS (no React/Vue) for the click + fetch + rendering.
- Use progressive enhancement: if JS is disabled, show a helpful message.
- Must handle failure cases gracefully with friendly UI:
    - User denies location
    - Browser doesn’t support geolocation
    - Weather API error / rate limit
    - Missing/invalid lat/lon

## Security/cleanliness:

- Validate inputs server-side.
- Don’t expose API keys to the browser.
- Use .env for secrets.

## Testing

- Write quality tests for this application
- I want a high level of test coverage

You will grab weather data via the API provided by https://openweathermap.org/api

The openweathermap api key env name is `OPENWEATHER_API_KEY`

**This is not a MVP but a fully polished web application, that should be ready for production. No shortcuts, and code quality is KING. Use best practices with laravel.**

Make sure you read guidenlines at ....
