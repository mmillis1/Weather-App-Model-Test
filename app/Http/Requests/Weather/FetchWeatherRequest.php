<?php

namespace App\Http\Requests\Weather;

use Illuminate\Foundation\Http\FormRequest;

class FetchWeatherRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'units' => ['required', 'in:metric,imperial'],
        ];
    }

    public function messages(): array {
        return [
            'latitude.required' => 'We need your latitude to find weather near you.',
            'latitude.numeric' => 'Latitude must be a valid number.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.required' => 'We need your longitude to complete the forecast.',
            'longitude.numeric' => 'Longitude must be a valid number.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'units.required' => 'Weather units are required.',
            'units.in' => 'Units must be metric or imperial.',
        ];
    }
}
