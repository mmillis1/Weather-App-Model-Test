<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Weather</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex items-center justify-center p-6">
        <noscript>
            <div class="bg-[#fff2f2] dark:bg-[#1D0002] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6 max-w-md text-center">
                <p class="text-[#1b1b18] dark:text-[#EDEDEC]">JavaScript is required to use this weather app. Please enable JavaScript in your browser.</p>
            </div>
        </noscript>

        <div class="w-full max-w-md">
            <div id="weather-app" class="space-y-6">
                <!-- Main Button -->
                <div class="text-center">
                    <button
                        id="get-weather-btn"
                        class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white bg-[#f53003] dark:bg-[#FF4433] rounded-lg border border-[#f53003] dark:border-[#FF4433] hover:bg-[#1b1b18] dark:hover:bg-[#EDEDEC] hover:border-[#1b1b18] dark:hover:border-[#EDEDEC] hover:text-[#FDFDFC] dark:hover:text-[#0a0a0a] transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span id="button-text">Get My Weather</span>
                        <svg id="loading-spinner" class="hidden animate-spin -mr-1 ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Error Message Container -->
                <div id="error-message" class="hidden bg-[#fff2f2] dark:bg-[#1D0002] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-4 text-center">
                    <p class="text-[#f53003] dark:text-[#FF4433] font-medium" id="error-text"></p>
                </div>

                <!-- Weather Card Container -->
                <div id="weather-card-container" class="hidden"></div>
            </div>
        </div>
    </body>
</html>
