<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeatherRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'lat' => ['required', 'numeric', 'min:-90', 'max:90'],
            'lon' => ['required', 'numeric', 'min:-180', 'max:180'],
        ];
    }

    public function messages(): array {
        return [
            'lat.required' => 'Latitude is required.',
            'lat.numeric' => 'Latitude must be a valid number.',
            'lat.min' => 'Latitude must be between -90 and 90 degrees.',
            'lat.max' => 'Latitude must be between -90 and 90 degrees.',
            'lon.required' => 'Longitude is required.',
            'lon.numeric' => 'Longitude must be a valid number.',
            'lon.min' => 'Longitude must be between -180 and 180 degrees.',
            'lon.max' => 'Longitude must be between -180 and 180 degrees.',
        ];
    }
}
