<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetWeatherRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array {
        return [
            'lat.required' => 'Latitude is required to fetch your weather.',
            'lat.numeric' => 'Latitude must be a valid number.',
            'lat.between' => 'Latitude must be between -90 and 90.',
            'lon.required' => 'Longitude is required to fetch your weather.',
            'lon.numeric' => 'Longitude must be a valid number.',
            'lon.between' => 'Longitude must be between -180 and 180.',
        ];
    }
}
