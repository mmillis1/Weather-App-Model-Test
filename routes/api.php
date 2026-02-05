<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::post('/weather', WeatherController::class);
