<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WeatherController::class, 'index'])->name('home');

Route::post('/api/weather', [WeatherController::class, 'getWeather'])
    ->middleware('throttle:30,1')
    ->name('weather.get');
