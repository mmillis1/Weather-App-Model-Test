<?php

use App\Http\Controllers\Api\Weather\ShowWeatherController;
use Illuminate\Support\Facades\Route;

Route::post('/weather/current', ShowWeatherController::class)->name('api.weather.current');
