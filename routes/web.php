<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WeatherController::class, 'index'])->name('home');
Route::get('/api/weather', [WeatherController::class, 'show'])->name('weather.show');
